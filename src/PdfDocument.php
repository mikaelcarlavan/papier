<?php

declare(strict_types=1);

namespace Papier;

use Papier\AcroForm\AcroForm;
use Papier\Content\ContentStream;
use Papier\Destination\XYZDestination;
use Papier\Encryption\StandardSecurityHandler;
use Papier\Font\{Font, TrueTypeFont, Type1Font};
use Papier\LogicalStructure\StructTreeRoot;
use Papier\Metadata\{DocumentInfo, XmpMetadata};
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfString};
use Papier\OptionalContent\OCProperties;
use Papier\Parser\{ImportedPage, PdfParser};
use Papier\Structure\{PageLabelStyle, PdfOutline, PdfPage};
use Papier\Writer\PdfWriter;

/**
 * High-level PDF document facade (ISO 32000-1).
 *
 * `PdfDocument` is the recommended entry point for creating new PDF files.
 * It wraps {@see PdfWriter} with a fluent API for pages, fonts, metadata,
 * forms, outlines, and optional content.
 *
 * Quick-start example:
 *
 *   use Papier\PdfDocument;
 *   use Papier\Elements\{Text, Rectangle, Color};
 *
 *   $doc  = PdfDocument::create();
 *   $doc->setTitle('My Document')->setAuthor('Jane Doe');
 *
 *   $f    = $doc->addFont('Helvetica');
 *   $page = $doc->addPage();
 *   $page->add(
 *       Rectangle::create(0, 780, 595, 61)->fill(Color::rgb(0.2, 0.3, 0.6)),
 *       Text::write('Hello, World!')->at(72, 800)->font($f, 24)->color(Color::white()),
 *   );
 *   $doc->save('/tmp/hello.pdf');
 *
 * For reading existing PDFs see {@see self::open()} which returns a
 * {@see \Papier\Parser\PdfParser}.
 */
final class PdfDocument
{
    private PdfWriter    $writer;
    private DocumentInfo $info;
    private XmpMetadata  $xmp;

    private function __construct(string $version = '1.7')
    {
        $this->writer = new PdfWriter($version);
        $this->info   = new DocumentInfo();
        $this->xmp    = new XmpMetadata();

        $this->info->setCreationDate();
        $this->info->setProducer('Papier PDF Library (ISO 32000-1)');

        $this->writer->setInfo($this->info);
        $this->writer->setXmpMetadata($this->xmp);
    }

    // ── Factory methods ───────────────────────────────────────────────────────

    /**
     * Create a new, empty PDF document.
     *
     * @param string $version  PDF version string written to the header (default `1.7`).
     */
    public static function create(string $version = '1.7'): self
    {
        return new self($version);
    }

    /**
     * Open an existing PDF file for reading.
     *
     * Returns a fully-parsed {@see PdfParser} object.  Use its
     * `getObjects()` / `getCatalog()` methods to inspect document content.
     *
     * @param string $path  Absolute or relative file-system path.
     */
    public static function open(string $path): PdfParser
    {
        $parser = PdfParser::fromFile($path);
        $parser->parse();
        return $parser;
    }

    /**
     * Parse a PDF document from a raw byte string.
     *
     * @param string $data  Complete PDF byte sequence.
     */
    public static function fromString(string $data): PdfParser
    {
        $parser = new PdfParser($data);
        $parser->parse();
        return $parser;
    }

    /**
     * Open an existing PDF for incremental update (ISO 32000-1 §7.5.6).
     *
     * Returns an {@see \Papier\Writer\IncrementalUpdater} that appends a new
     * revision to the original bytes rather than rewriting the file.  Use it to
     * edit metadata, add objects, or prepare a document for signing without
     * disturbing the existing content.
     *
     * @param string $path      Path to the source PDF.
     * @param string $password  Password, if the document is encrypted.
     */
    public static function openForUpdate(string $path, string $password = ''): \Papier\Writer\IncrementalUpdater
    {
        $parser = PdfParser::fromFile($path);
        if ($password !== '') {
            $parser->setPassword($password);
        }
        $parser->parse();
        return new \Papier\Writer\IncrementalUpdater($parser);
    }

    // ── Document metadata ─────────────────────────────────────────────────────

    /**
     * Set the document title (`/Title` in the document info dictionary and XMP).
     *
     * @param string $title  Document title (UTF-8).
     */
    public function setTitle(string $title): static
    {
        $this->info->setTitle($title);
        $this->xmp->setTitle($title);
        return $this;
    }

    /**
     * Set the document author (`/Author` / XMP `dc:creator`).
     *
     * @param string $author  Author name (UTF-8).
     */
    public function setAuthor(string $author): static
    {
        $this->info->setAuthor($author);
        $this->xmp->setAuthor($author);
        return $this;
    }

    /**
     * Set the document subject (`/Subject` / XMP `dc:description`).
     *
     * @param string $subject  Subject text (UTF-8).
     */
    public function setSubject(string $subject): static
    {
        $this->info->setSubject($subject);
        $this->xmp->setSubject($subject);
        return $this;
    }

    /**
     * Set document keywords (`/Keywords` / XMP `pdf:Keywords`).
     *
     * @param string $keywords  Space- or comma-separated keywords (UTF-8).
     */
    public function setKeywords(string $keywords): static
    {
        $this->info->setKeywords($keywords);
        $this->xmp->setKeywords($keywords);
        return $this;
    }

    /**
     * Set the name of the application that created the document (`/Creator`).
     *
     * @param string $creator  Application name (UTF-8).
     */
    public function setCreator(string $creator): static
    {
        $this->info->setCreator($creator);
        $this->xmp->setCreator($creator);
        return $this;
    }

    // ── Pages ─────────────────────────────────────────────────────────────────

    /**
     * Create a new blank page, append it to the document, and return it.
     *
     * Default size is ISO A4 (595.28 × 841.89 pt).  Use the
     * {@see PdfPage::SIZE_*} constants to pass standard dimensions:
     *
     *   $page = $doc->addPage(...PdfPage::SIZE_LETTER);
     *
     * @param float $width   Page width in points (1 pt = 1/72 inch).
     * @param float $height  Page height in points.
     */
    public function addPage(float $width = 595.28, float $height = 841.89): PdfPage
    {
        $page = new PdfPage($width, $height);
        $this->attachFontMetrics($page);
        $this->writer->addPage($page);
        return $page;
    }

    /**
     * Append a pre-built {@see PdfPage} to the document.
     *
     * @param PdfPage $page  A fully-configured page object.
     */
    public function appendPage(PdfPage $page): static
    {
        $this->attachFontMetrics($page);
        $this->writer->addPage($page);
        return $this;
    }

    /**
     * Give the page's resources a live resolver from font resource names to the
     * registered {@see Font} metrics objects, so elements can measure text using
     * real glyph advance widths (matching what is rendered).
     */
    private function attachFontMetrics(PdfPage $page): void
    {
        $page->getResources()->setFontMetricsResolver(
            fn (string $name): ?Font => $this->writer->getFontMetrics($name)
        );
    }

    /**
     * Import a page from another PDF and append it to this document.
     *
     * The source page is embedded as a Form XObject (ISO 32000-1 §8.10).
     * All resources referenced by that page — fonts, images, colour spaces,
     * graphics states — are deep-copied so the output document is fully
     * self-contained.
     *
     * The returned {@see PdfPage} has the same dimensions as the source page.
     * Add elements or content streams to it to overlay text, signatures, or
     * other graphics on top of the imported background.
     *
     * Example — mail-merge over state-provided blank forms:
     *
     *   $source   = PdfDocument::open('blank_form.pdf');
     *   $font     = $doc->addFont('Helvetica');
     *
     *   foreach ($customers as $customer) {
     *       foreach (range(1, $source->getPageCount()) as $pageNum) {
     *           $imported = ImportedPage::fromParser($source, $pageNum);
     *           $page     = $doc->importPage($imported);
     *           $page->add(
     *               Text::write($customer->name)->at(150, 620)->font($font, 11),
     *               Text::write($customer->address)->at(150, 605)->font($font, 11),
     *           );
     *       }
     *   }
     *   $doc->output('deal-jacket.pdf');
     *
     * @param ImportedPage $imported  Page prepared with {@see ImportedPage::fromParser()}.
     * @param float        $x        X offset of the imported page origin in points (default 0).
     * @param float        $y        Y offset of the imported page origin in points (default 0).
     * @param float        $scale    Uniform scale factor (1.0 = 100 %, full size).
     *
     * @return PdfPage  The new page, ready for overlay content.
     */
    public function importPage(
        ImportedPage $imported,
        float $x     = 0.0,
        float $y     = 0.0,
        float $scale = 1.0,
    ): PdfPage {
        $page = new PdfPage(
            $imported->getWidth()  * $scale,
            $imported->getHeight() * $scale,
        );
        $this->attachFontMetrics($page);

        $name = $imported->getResourceName();
        $page->getResources()->addXObject($name, $imported->getFormXObject());

        $cs = new ContentStream();
        $cs->save();
        if ($x !== 0.0 || $y !== 0.0 || $scale !== 1.0) {
            $cs->transform($scale, 0.0, 0.0, $scale, $x, $y);
        }
        $cs->drawXObject($name)->restore();
        $page->addContent($cs);

        $this->writer->addPage($page);
        return $page;
    }

    /**
     * Import pages from another PDF into this document.
     *
     * @param string|PdfParser $source  Path to a PDF, or an already-parsed PdfParser.
     * @param int[]|null       $pages   1-based page numbers to import (null = all).
     *
     * @return PdfPage[]  The newly created pages (in order), ready for overlays.
     */
    public function importPages(string|PdfParser $source, ?array $pages = null): array
    {
        $parser = is_string($source) ? self::open($source) : $source;
        $count  = $parser->getPageCount();
        $pages ??= range(1, $count);

        $result = [];
        foreach ($pages as $n) {
            if ($n < 1 || $n > $count) {
                continue;
            }
            $result[] = $this->importPage(ImportedPage::fromParser($parser, $n));
        }
        return $result;
    }

    /**
     * Merge several PDFs into one file, concatenating all their pages.
     *
     * @param string[] $sources   Paths to the source PDFs, in order.
     * @param string   $outPath   Destination path.
     */
    public static function merge(array $sources, string $outPath): void
    {
        $doc = self::create();
        foreach ($sources as $src) {
            $doc->importPages($src);
        }
        $doc->save($outPath);
    }

    /**
     * Extract a subset of pages from a PDF into a new file.
     *
     * @param string $source   Source PDF path.
     * @param int[]  $pages    1-based page numbers to keep, in the desired order.
     * @param string $outPath  Destination path.
     */
    public static function extractPages(string $source, array $pages, string $outPath): void
    {
        $doc = self::create();
        $doc->importPages($source, $pages);
        $doc->save($outPath);
    }

    /**
     * Place several source pages per sheet (N-up) and write the result.
     *
     * @param string $source   Source PDF path.
     * @param int    $cols     Columns per sheet.
     * @param int    $rows     Rows per sheet.
     * @param string $outPath  Destination path.
     * @param float  $sheetW   Output sheet width (points; default A4 portrait).
     * @param float  $sheetH   Output sheet height (points).
     * @param float  $margin   Outer margin (points).
     * @param float  $gutter   Gap between cells (points).
     */
    public static function nUp(
        string $source,
        int    $cols,
        int    $rows,
        string $outPath,
        float  $sheetW = 595.28,
        float  $sheetH = 841.89,
        float  $margin = 20.0,
        float  $gutter = 10.0,
    ): void {
        $parser  = self::open($source);
        $total   = $parser->getPageCount();
        $perSheet = max(1, $cols * $rows);

        $cellW = ($sheetW - 2 * $margin - ($cols - 1) * $gutter) / $cols;
        $cellH = ($sheetH - 2 * $margin - ($rows - 1) * $gutter) / $rows;

        $doc = self::create();
        for ($start = 1; $start <= $total; $start += $perSheet) {
            $sheet = $doc->addPage($sheetW, $sheetH);
            for ($k = 0; $k < $perSheet; $k++) {
                $pageNum = $start + $k;
                if ($pageNum > $total) {
                    break;
                }
                $imported = ImportedPage::fromParser($parser, $pageNum);
                $scale = min($cellW / max(1, $imported->getWidth()), $cellH / max(1, $imported->getHeight()));

                $col = $k % $cols;
                $row = intdiv($k, $cols);
                // Top-left origin within the grid; PDF y grows upward.
                $cellX = $margin + $col * ($cellW + $gutter);
                $cellY = $sheetH - $margin - ($row + 1) * $cellH - $row * $gutter;
                // Centre the scaled page inside its cell.
                $x = $cellX + ($cellW - $imported->getWidth()  * $scale) / 2;
                $y = $cellY + ($cellH - $imported->getHeight() * $scale) / 2;

                $name = 'NUp' . $pageNum;
                $sheet->getResources()->addXObject($name, $imported->getFormXObject());
                $cs = new ContentStream();
                $cs->save()->transform($scale, 0, 0, $scale, $x, $y)->drawXObject($name)->restore();
                $sheet->addContent($cs);
            }
        }
        $doc->save($outPath);
    }

    // ── Fonts ─────────────────────────────────────────────────────────────────

    /**
     * Add a font and return its resource name.
     *
     * Pass either a standard Type 1 font name **or** a path to a `.ttf` /
     * `.otf` file.  The library detects which case applies automatically.
     *
     * Standard Type 1 font names (case-sensitive):
     *   `Helvetica`, `Helvetica-Bold`, `Helvetica-Oblique`, `Helvetica-BoldOblique`,
     *   `Times-Roman`, `Times-Bold`, `Times-Italic`, `Times-BoldItalic`,
     *   `Courier`, `Courier-Bold`, `Courier-Oblique`, `Courier-BoldOblique`,
     *   `Symbol`, `ZapfDingbats`.
     *
     * TTF / OTF file path:
     *   The PostScript name is extracted automatically from the font binary.
     *   Glyph metrics and the full font program are embedded in the PDF.
     *
     * The returned resource name is passed to `Text::write()->font($name, $size)`
     * and `setDefaultAppearance($name, $size)`.
     *
     * Example:
     *
     *   $sans    = $doc->addFont('Helvetica');
     *   $heading = $doc->addFont(__DIR__ . '/fonts/Lato-Bold.ttf');
     *
     * @param string $baseFont      Standard font name or path to a TTF/OTF file.
     * @param string $resourceName  Override the auto-generated resource key (e.g. `F1`).
     *
     * @return string  Resource name used in content streams (e.g. `F1`).
     */
    public function addFont(string $baseFont, string $resourceName = '', bool $subset = false): string
    {
        $ext = strtolower(pathinfo($baseFont, PATHINFO_EXTENSION));
        if ($ext === 'ttf' || $ext === 'otf' || file_exists($baseFont)) {
            $font = TrueTypeFont::fromFile($baseFont);
            if ($subset) {
                $font->setSubset(true);
            }
        } else {
            $font = new Type1Font($baseFont);
        }
        return $this->writer->registerFont($font, $resourceName);
    }

    /**
     * Add a full-Unicode composite (Type 0) font from a TrueType/OpenType file
     * and return its resource name.
     *
     * Unlike {@see addFont()} — which produces a single-byte WinAnsi font
     * limited to 256 codes — this embeds the font as a CIDFontType2 with
     * Identity-H encoding, giving access to every glyph in the file (CJK,
     * Cyrillic, Greek, etc.).  A /ToUnicode CMap is generated automatically so
     * the text stays searchable, and unused glyphs are subset out by default.
     *
     * @param string $path          Path to a .ttf/.otf file.
     * @param string $resourceName  Override the auto-generated resource key.
     * @param bool   $subset        Strip unused glyph outlines (default true).
     *
     * @return string  Resource name used in content streams.
     */
    public function addUnicodeFont(string $path, string $resourceName = '', bool $subset = true): string
    {
        $font = \Papier\Font\Type0Font::fromTrueType($path, $subset);
        return $this->writer->registerFont($font, $resourceName);
    }

    /**
     * Register any {@see Font} subclass (TrueType, Type 0, Type 3, etc.) and
     * return its resource name.
     *
     * @param Font   $font          Font object to register.
     * @param string $resourceName  Override the auto-generated resource key.
     *
     * @return string  Resource name.
     */
    public function registerFont(Font $font, string $resourceName = ''): string
    {
        return $this->writer->registerFont($font, $resourceName);
    }

    // ── Document features ─────────────────────────────────────────────────────

    /**
     * Attach a bookmark outline tree to the document (`/Outlines`).
     *
     * Build the tree with {@see PdfOutline} and its `addItem()` method.
     *
     * @param PdfOutline $outline  The root outline object.
     */
    public function setOutline(\Papier\Structure\PdfOutline $outline): static
    {
        $this->writer->setOutline($outline);
        return $this;
    }

    /**
     * Attach an interactive form to the document (`/AcroForm`).
     *
     * Build the form with {@see AcroForm} and register fields using
     * both `$form->addField()` and `$page->addFormField()`.
     *
     * @param AcroForm $form  The AcroForm root dictionary.
     */
    public function setAcroForm(AcroForm $form): static
    {
        $this->writer->setAcroForm($form);
        return $this;
    }

    /**
     * Attach optional-content (layer) properties to the document (`/OCProperties`).
     *
     * @param OCProperties $oc  The optional-content properties object.
     */
    public function setOCProperties(OCProperties $oc): static
    {
        $this->writer->setOCProperties($oc);
        return $this;
    }

    /**
     * Attach a logical structure tree to the document (`/StructTreeRoot`).
     *
     * Required for tagged PDF (PDF/UA, accessibility).
     *
     * @param StructTreeRoot $st  The structure tree root.
     */
    public function setStructTree(StructTreeRoot $st): static
    {
        $this->writer->setStructTree($st);
        return $this;
    }

    /**
     * Set viewer preference flags (`/ViewerPreferences`) (§12.2 Table 150).
     *
     * @param array<string, bool|string|int> $prefs  Key-value pairs, e.g.:
     *   `'HideToolbar'           => true`,
     *   `'HideMenubar'           => true`,
     *   `'FitWindow'             => true`,
     *   `'PrintScaling'          => 'None'`,
     *   `'Duplex'                => 'DuplexFlipLongEdge'`,
     *   `'NonFullScreenPageMode' => 'UseNone'`.
     */
    public function setViewerPreferences(array $prefs): static
    {
        $dict = new PdfDictionary();
        foreach ($prefs as $key => $value) {
            if (is_bool($value)) {
                $dict->set($key, $value ? new \Papier\Objects\PdfBoolean(true) : new \Papier\Objects\PdfBoolean(false));
            } elseif (is_string($value)) {
                $dict->set($key, new PdfName($value));
            } elseif (is_int($value)) {
                $dict->set($key, new \Papier\Objects\PdfInteger($value));
            }
        }
        $this->writer->setViewerPreferences($dict);
        return $this;
    }

    /**
     * Register a named destination accessible by name from GoTo actions,
     * outline items, and hyperlinks (§12.3.2).
     *
     * Named destinations are stored in `/Names /Dests` in the catalog.  Once
     * registered, the name can be passed to `GoToAction` or outline items
     * instead of an explicit page reference.
     *
     * Example:
     *
     *   $page = $doc->addPage();
     *   $doc->addNamedDestination('chapter-2', $page, 0, 750);
     *
     * @param string    $name   Unique name for this destination.
     * @param PdfPage   $page   Target page object.
     * @param float     $left   Left position (null = inherit current scroll).
     * @param float     $top    Top position (null = inherit current scroll).
     * @param float     $zoom   Zoom factor, or 0 to leave unchanged (null = inherit).
     */
    public function addNamedDestination(
        string  $name,
        PdfPage $page,
        ?float  $left = null,
        ?float  $top  = null,
        ?float  $zoom = null,
    ): static {
        $dest = XYZDestination::create($page->getDictionary(), $left, $top, $zoom);
        $this->writer->addNamedDestination($name, $dest);
        return $this;
    }

    /**
     * Set the document open action — scroll the viewer to a specific page and
     * position when the document is opened (§12.3.2, /OpenAction).
     *
     * @param PdfPage $page   Page to display on open.
     * @param float   $left   Left coordinate (null = inherit).
     * @param float   $top    Top coordinate (null = inherit).
     * @param float   $zoom   Zoom level (null = inherit; 0 = fit).
     */
    public function setOpenDestination(
        PdfPage $page,
        ?float  $left = null,
        ?float  $top  = null,
        ?float  $zoom = null,
    ): static {
        $dest = XYZDestination::create($page->getDictionary(), $left, $top, $zoom);
        $this->writer->setOpenAction($dest);
        return $this;
    }

    /**
     * Add a page-label range (§12.4.2).
     *
     * Page labels define custom numbering shown in the viewer thumbnail strip
     * and "go to page" dialogs.  The range starts at `$startPage` (0-based)
     * and continues until the next label range begins.
     *
     * Example — front matter as lowercase roman, body as decimal:
     *
     *   $doc->addPageLabel(0, PageLabelStyle::RomanLower);            // i, ii, iii …
     *   $doc->addPageLabel(4, PageLabelStyle::Decimal);               // 1, 2, 3 …
     *   $doc->addPageLabel(4, PageLabelStyle::Decimal, prefix: 'p.'); // p.1, p.2 …
     *
     * @param int            $startPage   0-based page index where this range begins.
     * @param PageLabelStyle $style       Numbering style; defaults to decimal arabic.
     * @param int            $startValue  First number in the range (default 1).
     * @param string         $prefix      Label prefix prepended to every number (e.g. `'p.'`).
     */
    public function addPageLabel(
        int            $startPage,
        PageLabelStyle $style      = PageLabelStyle::Decimal,
        int            $startValue = 1,
        string         $prefix     = '',
    ): static {
        $labelDict = new PdfDictionary();
        if ($style !== PageLabelStyle::None) {
            $labelDict->set('S', new PdfName($style->value));
        }
        if ($prefix !== '') {
            $labelDict->set('P', new PdfString($prefix));
        }
        if ($startValue !== 1) {
            $labelDict->set('St', new PdfInteger($startValue));
        }
        $this->writer->addPageLabel($startPage, $labelDict);
        return $this;
    }

    /**
     * Embed a file attachment in the document (§7.11.4, /Names /EmbeddedFiles).
     *
     * Attached files appear in the viewer's Attachments or Paperclip panel.
     * Use for supplementary data, source files, or supporting documents.
     *
     * Example:
     *
     *   $doc->attachFile('data.csv', file_get_contents('report.csv'), 'text/csv');
     *   $doc->attachFile('readme.txt', 'See the PDF for details.', 'text/plain');
     *
     * @param string $filename  Display name shown in the viewer.
     * @param string $data      Raw file contents (any binary or text).
     * @param string $mimeType  MIME type (default `application/octet-stream`).
     */
    public function attachFile(
        string $filename,
        string $data,
        string $mimeType = 'application/octet-stream',
    ): static {
        $this->writer->attachFile($filename, $data, $mimeType);
        return $this;
    }

    /**
     * Encrypt the document with a password and permission flags.
     *
     * Example — require a password to print or copy text:
     *
     *   $doc->encrypt('user123', 'owner456', StandardSecurityHandler::PERM_PRINT);
     *
     * @param string $userPassword   Password required to open the document
     *                               (empty string = no password to open).
     * @param string $ownerPassword  Password to bypass restrictions (defaults
     *                               to $userPassword when empty).
     * @param int    $permissions    Permission flags (OR the PERM_* constants).
     * @param int    $algorithm      Encryption algorithm constant:
     *                               `StandardSecurityHandler::RC4_40`,
     *                               `::RC4_128`, `::AES_128` (default), `::AES_256`.
     */
    public function encrypt(
        string $userPassword   = '',
        string $ownerPassword  = '',
        int    $permissions    = StandardSecurityHandler::PERM_ALL,
        int    $algorithm      = StandardSecurityHandler::AES_128,
    ): static {
        $handler = new StandardSecurityHandler($userPassword, $ownerPassword, $permissions, $algorithm);
        $this->writer->setEncryption($handler);
        return $this;
    }

    /**
     * Mark the document for PDF/A archival conformance (ISO 19005).
     *
     * Adds an sRGB OutputIntent, PDF/A identification metadata in XMP, and a
     * document ID.  You remain responsible for embedding every font
     * (use {@see addFont()} with a TTF/OTF path or {@see addUnicodeFont()}, not
     * the standard 14 fonts) and avoiding disallowed features (encryption,
     * JavaScript, external references).
     *
     * @param int    $part         PDF/A part: 1, 2 (default), or 3.
     * @param string $conformance  Conformance level: 'B' (visual) or 'A' (accessible).
     */
    public function enablePdfA(int $part = 2, string $conformance = 'B'): static
    {
        $this->xmp->setPdfAConformance($part, $conformance);
        $this->writer->enablePdfA($part, $conformance);
        return $this;
    }

    /**
     * Enable compressed object streams and a cross-reference stream (PDF 1.5+).
     *
     * Produces significantly smaller files by packing non-stream objects into
     * compressed object streams.  Ignored when the document is encrypted.
     */
    public function useObjectStreams(bool $enabled = true): static
    {
        $this->writer->setUseObjectStreams($enabled);
        return $this;
    }

    // ── Running (repeating) elements ────────────────────────────────────────────

    /** @var array<int, array{render:\Closure, when:string|int|\Closure}> */
    private array $runningElements = [];
    /** @var array<int, int> spl_object_id(page) → content-stream count before overlays */
    private array $pageBaseCounts = [];

    /**
     * Repeat element(s) on pages matching a rule (headers, footers, watermarks…).
     *
     * The callback runs once per matching page at output time, when the total
     * page count is known, and adds elements to that page:
     *
     *   $doc->onEachPage(function (PdfPage $page, int $n, int $total) use ($font) {
     *       $page->add(Text::write("Page $n of $total")->at(72, 30)->font($font, 9));
     *   });
     *
     * @param \Closure $render  fn(PdfPage $page, int $pageNumber, int $pageCount): void
     * @param string|int|\Closure $when  'all'|'odd'|'even'|'first'|'last', an int N
     *        (every Nth page), or fn(int $pageNumber, int $pageCount): bool.
     */
    public function onEachPage(\Closure $render, string|int|\Closure $when = 'all'): static
    {
        $this->runningElements[] = ['render' => $render, 'when' => $when];
        return $this;
    }

    /** Convenience: a running header (identical to {@see onEachPage()}). */
    public function header(\Closure $render, string|int|\Closure $when = 'all'): static
    {
        return $this->onEachPage($render, $when);
    }

    /** Convenience: a running footer (identical to {@see onEachPage()}). */
    public function footer(\Closure $render, string|int|\Closure $when = 'all'): static
    {
        return $this->onEachPage($render, $when);
    }

    /** Render the running elements onto every matching page (idempotent). */
    private function applyRunningElements(): void
    {
        if (empty($this->runningElements)) {
            return;
        }
        $pages = $this->writer->getPages();
        $total = count($pages);
        foreach ($pages as $i => $page) {
            $id = spl_object_id($page);
            // Record (once) the page's own content count, then strip prior overlays.
            $this->pageBaseCounts[$id] ??= $page->getContentStreamCount();
            $page->truncateContentStreams($this->pageBaseCounts[$id]);

            $num = $i + 1;
            foreach ($this->runningElements as $re) {
                if ($this->pageMatches($re['when'], $num, $total)) {
                    ($re['render'])($page, $num, $total);
                }
            }
        }
    }

    private function pageMatches(string|int|\Closure $when, int $num, int $total): bool
    {
        if ($when instanceof \Closure) {
            return (bool) $when($num, $total);
        }
        if (is_int($when)) {
            return $when > 0 && $num % $when === 0;
        }
        return match ($when) {
            'odd'   => $num % 2 === 1,
            'even'  => $num % 2 === 0,
            'first' => $num === 1,
            'last'  => $num === $total,
            default => true, // 'all'
        };
    }

    // ── Output ────────────────────────────────────────────────────────────────

    /**
     * Generate the complete PDF and return it as a byte string.
     *
     * Suitable for storing in a database, sending via an API response, or
     * piping to another process.
     */
    public function toString(): string
    {
        $this->applyRunningElements();
        return $this->writer->generate();
    }

    /**
     * Generate the PDF and write it to a file.
     *
     * @param string $path  Destination file path (created or overwritten).
     */
    public function save(string $path): void
    {
        $result = file_put_contents($path, $this->toString());
        if ($result === false) {
            throw new \RuntimeException("Cannot write PDF to: $path");
        }
    }

    /**
     * Stream the PDF directly to the HTTP response.
     *
     * Sends `Content-Type: application/pdf` and `Content-Disposition` headers
     * then echoes the raw PDF bytes.  Call before any other output.
     *
     * @param string $filename  Suggested file name for the download dialog.
     * @param bool   $inline    true → display in-browser; false → force download.
     */
    public function output(string $filename = 'document.pdf', bool $inline = true): void
    {
        $pdf         = $this->toString();
        $disposition = $inline ? 'inline' : 'attachment';

        header('Content-Type: application/pdf');
        header("Content-Disposition: {$disposition}; filename=\"{$filename}\"");
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdf;
    }

    // ── Direct access ─────────────────────────────────────────────────────────

    /** Return the underlying {@see PdfWriter} for advanced serialisation control. */
    public function getWriter(): PdfWriter  { return $this->writer; }

    /** Return the {@see DocumentInfo} object for direct metadata manipulation. */
    public function getInfo(): DocumentInfo { return $this->info; }
}
