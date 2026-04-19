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
use Papier\Parser\PdfParser;
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
        $this->writer->addPage($page);
        return $this;
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
    public function addFont(string $baseFont, string $resourceName = ''): string
    {
        $ext = strtolower(pathinfo($baseFont, PATHINFO_EXTENSION));
        if ($ext === 'ttf' || $ext === 'otf' || file_exists($baseFont)) {
            $font = TrueTypeFont::fromFile($baseFont);
        } else {
            $font = new Type1Font($baseFont);
        }
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

    // ── Output ────────────────────────────────────────────────────────────────

    /**
     * Generate the complete PDF and return it as a byte string.
     *
     * Suitable for storing in a database, sending via an API response, or
     * piping to another process.
     */
    public function toString(): string
    {
        return $this->writer->generate();
    }

    /**
     * Generate the PDF and write it to a file.
     *
     * @param string $path  Destination file path (created or overwritten).
     */
    public function save(string $path): void
    {
        $this->writer->save($path);
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
