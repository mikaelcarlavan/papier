<?php

declare(strict_types=1);

namespace Papier\Parser;

use Papier\Objects\{
    PdfArray, PdfBoolean, PdfDictionary, PdfIndirectReference,
    PdfInteger, PdfName, PdfNull, PdfObject, PdfReal, PdfStream, PdfString
};

/**
 * High-level PDF parser (ISO 32000-1 §7.5).
 *
 * Reads a PDF byte stream, parses the cross-reference table, resolves
 * indirect objects, and exposes a document model.
 */
final class PdfParser
{
    private Tokenizer           $tok;
    private CrossReferenceTable $xref;
    private ObjectParser        $objParser;

    /** @var array<int, PdfObject> Resolved object cache */
    private array $objectCache = [];

    private ?PdfDictionary $catalog = null;
    private ?PdfDictionary $info    = null;

    public function __construct(private readonly string $data)
    {
        $this->tok       = new Tokenizer($data);
        $this->xref      = new CrossReferenceTable();
        $this->objParser = new ObjectParser($this->tok);
    }

    public static function fromFile(string $path): self
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \InvalidArgumentException("Cannot read file: $path");
        }
        return new self($data);
    }

    /** Parse the PDF and build the in-memory object graph. */
    public function parse(): void
    {
        // Verify header
        if (!str_starts_with($this->data, '%PDF-')) {
            throw new \RuntimeException('Not a PDF file (missing %%PDF- header).');
        }

        $this->xref->parse($this->tok);

        // Resolve catalog
        $catNum = $this->xref->getCatalogObjectNumber();
        if ($catNum === null) {
            throw new \RuntimeException('No document catalog found.');
        }
        $catObj = $this->resolveObject($catNum);
        if ($catObj instanceof PdfDictionary) {
            $this->catalog = $catObj;
        } elseif ($catObj instanceof PdfStream) {
            $this->catalog = $catObj->getDictionary();
        }

        // Resolve info
        $infoNum = $this->xref->getInfoObjectNumber();
        if ($infoNum !== null) {
            $infoObj = $this->resolveObject($infoNum);
            if ($infoObj instanceof PdfDictionary) {
                $this->info = $infoObj;
            }
        }
    }

    // ── Object resolution ─────────────────────────────────────────────────────

    /**
     * Resolve an indirect object by number, following the xref.
     */
    public function resolveObject(int $objNum, int $genNum = 0): ?PdfObject
    {
        if (isset($this->objectCache[$objNum])) {
            return $this->objectCache[$objNum];
        }

        $offset = $this->xref->getOffset($objNum);
        if ($offset === null) {
            return null;
        }

        $this->tok->setPosition($offset);
        $this->tok->skipWhitespace();

        // Read `n g obj`
        $n = $this->tok->nextToken();
        $g = $this->tok->nextToken();
        $o = $this->tok->nextToken();

        if ($o['value'] !== 'obj') {
            return null;
        }

        $obj = $this->objParser->parseObject();
        $obj->setObjectNumber($objNum, $genNum);

        $this->objectCache[$objNum] = $obj;
        return $obj;
    }

    /**
     * Resolve an indirect reference to a concrete object.
     */
    public function resolve(PdfObject $obj): PdfObject
    {
        if ($obj instanceof PdfIndirectReference) {
            return $this->resolveObject($obj->getObjectNumber()) ?? $obj;
        }
        return $obj;
    }

    // ── Document navigation ───────────────────────────────────────────────────

    public function getCatalog(): ?PdfDictionary { return $this->catalog; }
    public function getInfo(): ?PdfDictionary    { return $this->info; }
    public function getFileId(): string          { return $this->xref->getFileId1(); }

    /** Return the PDF version string (e.g., '1.7'). */
    public function getVersion(): string
    {
        if (preg_match('/^%PDF-(\d\.\d)/', $this->data, $m)) {
            return $m[1];
        }
        return '1.7';
    }

    /**
     * Extract all pages as dictionaries.
     *
     * @return PdfDictionary[]
     */
    public function getPages(): array
    {
        if ($this->catalog === null) { return []; }

        $pagesRef = $this->catalog->get('Pages');
        if ($pagesRef === null) { return []; }

        $pagesObj = $this->resolve($pagesRef);
        if (!$pagesObj instanceof PdfDictionary) { return []; }

        return $this->collectPages($pagesObj);
    }

    /** @return PdfDictionary[] */
    private function collectPages(PdfDictionary $node): array
    {
        $type = $node->get('Type');
        if ($type instanceof PdfName && $type->getValue() === 'Page') {
            return [$node];
        }

        $kids = $node->get('Kids');
        if (!$kids instanceof PdfArray) { return []; }

        $pages = [];
        foreach ($kids as $kidRef) {
            $kid = $this->resolve($kidRef);
            if ($kid instanceof PdfDictionary) {
                array_push($pages, ...$this->collectPages($kid));
            }
        }
        return $pages;
    }

    /**
     * Extract plain text from a page's content stream(s).
     * This is a best-effort extraction; complex encodings may not decode correctly.
     */
    public function extractTextFromPage(PdfDictionary $page): string
    {
        $contentRef = $page->get('Contents');
        if ($contentRef === null) { return ''; }

        $contentObjs = [];
        if ($contentRef instanceof PdfArray) {
            foreach ($contentRef as $ref) {
                $contentObjs[] = $this->resolve($ref);
            }
        } else {
            $contentObjs[] = $this->resolve($contentRef);
        }

        $text = '';
        foreach ($contentObjs as $obj) {
            if ($obj instanceof PdfStream) {
                $decoded = $obj->decode();
                $text   .= $this->parseContentStreamForText($decoded);
            }
        }
        return $text;
    }

    /** Extract text strings from a raw content stream. */
    private function parseContentStreamForText(string $stream): string
    {
        $text = '';
        // Match Tj, TJ, ', " operators and their string operands
        // Tj: (string) Tj
        if (preg_match_all('/\(([^)]*)\)\s*(?:Tj|\'|"[^"]*")/u', $stream, $matches)) {
            foreach ($matches[1] as $s) {
                $text .= $this->unescapeString($s) . ' ';
            }
        }
        // TJ: [(string) …] TJ
        if (preg_match_all('/\[([^\]]*)\]\s*TJ/u', $stream, $matches)) {
            foreach ($matches[1] as $s) {
                if (preg_match_all('/\(([^)]*)\)/', $s, $inner)) {
                    foreach ($inner[1] as $part) {
                        $text .= $this->unescapeString($part);
                    }
                }
            }
            $text .= ' ';
        }
        return $text;
    }

    private function unescapeString(string $s): string
    {
        return stripcslashes(str_replace(
            ['\\(', '\\)', '\\\\'],
            ['(', ')', '\\'],
            $s
        ));
    }

    // ── Document information extraction ──────────────────────────────────────

    public function getTitle(): string  { return $this->getInfoString('Title'); }
    public function getAuthor(): string { return $this->getInfoString('Author'); }
    public function getSubject(): string{ return $this->getInfoString('Subject'); }
    public function getCreator(): string{ return $this->getInfoString('Creator'); }

    private function getInfoString(string $key): string
    {
        if ($this->info === null) { return ''; }
        $v = $this->info->get($key);
        return $v instanceof PdfString ? $this->decodePdfString($v->getValue()) : '';
    }

    /** Decode a raw PDF string value, stripping UTF-16BE BOM when present. */
    private function decodePdfString(string $val): string
    {
        if (str_starts_with($val, "\xFE\xFF")) {
            return mb_convert_encoding(substr($val, 2), 'UTF-8', 'UTF-16BE');
        }
        return $val;
    }

    // ── High-level extraction ─────────────────────────────────────────────────

    /**
     * Return the total number of pages in the document.
     */
    public function getPageCount(): int
    {
        return count($this->getPages());
    }

    /**
     * Extract plain text from all pages, concatenated with page breaks.
     *
     * @param string $separator  Inserted between pages (default: newline).
     */
    public function extractText(string $separator = "\n"): string
    {
        $parts = [];
        foreach ($this->getPages() as $page) {
            $t = trim($this->extractTextFromPage($page));
            if ($t !== '') {
                $parts[] = $t;
            }
        }
        return implode($separator, $parts);
    }

    /**
     * Extract text from a specific page (1-based index).
     *
     * @param int $pageNumber  1-based page number.
     *
     * @throws \OutOfRangeException  If the page number is out of range.
     */
    public function extractTextFromPageNumber(int $pageNumber): string
    {
        $pages = $this->getPages();
        if ($pageNumber < 1 || $pageNumber > count($pages)) {
            throw new \OutOfRangeException(
                "Page $pageNumber is out of range (document has " . count($pages) . ' pages).'
            );
        }
        return $this->extractTextFromPage($pages[$pageNumber - 1]);
    }

    /**
     * Extract all image XObjects from the document.
     *
     * Returns one entry per image found in any page's `/Resources /XObject`
     * sub-dictionary whose `/Subtype` is `/Image`.
     *
     * Each entry has the keys:
     *   - `page`   — 1-based page number
     *   - `name`   — resource name (e.g. `Im0`)
     *   - `width`  — image width in pixels (int)
     *   - `height` — image height in pixels (int)
     *   - `filter` — filter name string, or `''` if none
     *   - `data`   — decoded raw image bytes (string)
     *
     * @return array<int, array{page:int,name:string,width:int,height:int,filter:string,data:string}>
     */
    public function extractImages(): array
    {
        $images = [];
        foreach ($this->getPages() as $pi => $page) {
            $pageNum = $pi + 1;
            $resRef  = $page->get('Resources');
            if ($resRef === null) { continue; }

            $res = $this->resolve($resRef);
            if (!$res instanceof PdfDictionary) { continue; }

            $xobjRef = $res->get('XObject');
            if ($xobjRef === null) { continue; }

            $xobjs = $this->resolve($xobjRef);
            if (!$xobjs instanceof PdfDictionary) { continue; }

            foreach ($xobjs->getEntries() as $name => $ref) {
                $obj = $this->resolve($ref);
                if (!$obj instanceof PdfStream) { continue; }

                $dict    = $obj->getDictionary();
                $subtype = $dict->get('Subtype');
                if (!$subtype instanceof PdfName || $subtype->getValue() !== 'Image') {
                    continue;
                }

                $wObj = $dict->get('Width');
                $hObj = $dict->get('Height');
                $fObj = $dict->get('Filter');

                $images[] = [
                    'page'   => $pageNum,
                    'name'   => $name,
                    'width'  => $wObj instanceof PdfInteger ? $wObj->getValue() : 0,
                    'height' => $hObj instanceof PdfInteger ? $hObj->getValue() : 0,
                    'filter' => $fObj instanceof PdfName ? $fObj->getValue() : '',
                    'data'   => $obj->decode(),
                ];
            }
        }
        return $images;
    }

    /**
     * Return all fonts referenced in the document, keyed by resource name.
     *
     * Each entry has:
     *   - `name`     — resource name (e.g. `F1`)
     *   - `subtype`  — font subtype string (e.g. `Type1`, `TrueType`)
     *   - `baseFont` — `/BaseFont` value, or `''` if absent
     *   - `encoding` — `/Encoding` value, or `''` if absent
     *
     * @return array<int, array{name:string,subtype:string,baseFont:string,encoding:string}>
     */
    public function getFonts(): array
    {
        $seen  = [];
        $fonts = [];
        foreach ($this->getPages() as $page) {
            $resRef = $page->get('Resources');
            if ($resRef === null) { continue; }

            $res = $this->resolve($resRef);
            if (!$res instanceof PdfDictionary) { continue; }

            $fontDictRef = $res->get('Font');
            if ($fontDictRef === null) { continue; }

            $fontDict = $this->resolve($fontDictRef);
            if (!$fontDict instanceof PdfDictionary) { continue; }

            foreach ($fontDict->getEntries() as $resName => $ref) {
                if (isset($seen[$resName])) { continue; }
                $seen[$resName] = true;

                $fontObj = $this->resolve($ref);
                if (!$fontObj instanceof PdfDictionary) { continue; }

                $subtypeObj  = $fontObj->get('Subtype');
                $baseFontObj = $fontObj->get('BaseFont');
                $encodingObj = $fontObj->get('Encoding');

                $fonts[] = [
                    'name'     => $resName,
                    'subtype'  => $subtypeObj  instanceof PdfName   ? $subtypeObj->getValue()  : '',
                    'baseFont' => $baseFontObj instanceof PdfName   ? $baseFontObj->getValue() : '',
                    'encoding' => $encodingObj instanceof PdfName   ? $encodingObj->getValue() : '',
                ];
            }
        }
        return $fonts;
    }

    /**
     * Extract all annotations from the document.
     *
     * Each entry has:
     *   - `page`     — 1-based page number
     *   - `subtype`  — annotation subtype (e.g. `Text`, `Link`, `Widget`)
     *   - `rect`     — `[llx, lly, urx, ury]` float array, or `[]` if missing
     *   - `contents` — `/Contents` string, or `''` if absent
     *
     * @return array<int, array{page:int,subtype:string,rect:float[],contents:string}>
     */
    public function getAnnotations(): array
    {
        $annotations = [];
        foreach ($this->getPages() as $pi => $page) {
            $pageNum = $pi + 1;
            $annsRef = $page->get('Annots');
            if ($annsRef === null) { continue; }

            $annsObj = $this->resolve($annsRef);
            if (!$annsObj instanceof PdfArray) { continue; }

            foreach ($annsObj as $annRef) {
                $ann = $this->resolve($annRef);
                if (!$ann instanceof PdfDictionary) { continue; }

                $subtypeObj  = $ann->get('Subtype');
                $rectObj     = $ann->get('Rect');
                $contentsObj = $ann->get('Contents');

                $rect = [];
                if ($rectObj instanceof PdfArray) {
                    foreach ($rectObj as $v) {
                        $rect[] = ($v instanceof PdfReal || $v instanceof PdfInteger) ? (float)$v->getValue() : 0.0;
                    }
                }

                $annotations[] = [
                    'page'     => $pageNum,
                    'subtype'  => $subtypeObj  instanceof PdfName   ? $subtypeObj->getValue()  : '',
                    'rect'     => $rect,
                    'contents' => $contentsObj instanceof PdfString ? $this->decodePdfString($contentsObj->getValue()) : '',
                ];
            }
        }
        return $annotations;
    }

    /**
     * Return a structured summary of a single page (1-based).
     *
     * Keys:
     *   - `number`       — page number (int)
     *   - `width`        — media-box width in points (float)
     *   - `height`       — media-box height in points (float)
     *   - `fontNames`    — resource names of fonts used (string[])
     *   - `imageNames`   — resource names of image XObjects (string[])
     *   - `text`         — extracted text (string)
     *   - `annotations`  — array like {@see getAnnotations()} but without the `page` key
     *
     * @throws \OutOfRangeException
     *
     * @return array{number:int,width:float,height:float,fontNames:string[],imageNames:string[],text:string,annotations:array}
     */
    public function getPageInfo(int $pageNumber): array
    {
        $pages = $this->getPages();
        if ($pageNumber < 1 || $pageNumber > count($pages)) {
            throw new \OutOfRangeException(
                "Page $pageNumber is out of range (document has " . count($pages) . ' pages).'
            );
        }
        $page = $pages[$pageNumber - 1];

        // Media box
        $width = $height = 0.0;
        $mbRef = $page->get('MediaBox');
        if ($mbRef instanceof PdfArray) {
            $items  = $mbRef->getItems();
            $width  = isset($items[2]) && ($items[2] instanceof PdfReal || $items[2] instanceof PdfInteger)
                ? (float)$items[2]->getValue() : 0.0;
            $height = isset($items[3]) && ($items[3] instanceof PdfReal || $items[3] instanceof PdfInteger)
                ? (float)$items[3]->getValue() : 0.0;
        }

        // Resources
        $fontNames  = [];
        $imageNames = [];
        $resRef = $page->get('Resources');
        if ($resRef !== null) {
            $res = $this->resolve($resRef);
            if ($res instanceof PdfDictionary) {
                $fontDictRef = $res->get('Font');
                if ($fontDictRef !== null) {
                    $fontDict = $this->resolve($fontDictRef);
                    if ($fontDict instanceof PdfDictionary) {
                        $fontNames = array_keys($fontDict->getEntries());
                    }
                }
                $xobjRef = $res->get('XObject');
                if ($xobjRef !== null) {
                    $xobjs = $this->resolve($xobjRef);
                    if ($xobjs instanceof PdfDictionary) {
                        foreach ($xobjs->getEntries() as $name => $ref) {
                            $obj = $this->resolve($ref);
                            if ($obj instanceof PdfStream) {
                                $sub = $obj->getDictionary()->get('Subtype');
                                if ($sub instanceof PdfName && $sub->getValue() === 'Image') {
                                    $imageNames[] = $name;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Annotations
        $annots  = [];
        $annsRef = $page->get('Annots');
        if ($annsRef !== null) {
            $annsObj = $this->resolve($annsRef);
            if ($annsObj instanceof PdfArray) {
                foreach ($annsObj as $annRef) {
                    $ann = $this->resolve($annRef);
                    if (!$ann instanceof PdfDictionary) { continue; }
                    $subtypeObj  = $ann->get('Subtype');
                    $rectObj     = $ann->get('Rect');
                    $contentsObj = $ann->get('Contents');
                    $rect = [];
                    if ($rectObj instanceof PdfArray) {
                        foreach ($rectObj as $v) {
                            $rect[] = ($v instanceof PdfReal || $v instanceof PdfInteger) ? (float)$v->getValue() : 0.0;
                        }
                    }
                    $annots[] = [
                        'subtype'  => $subtypeObj  instanceof PdfName   ? $subtypeObj->getValue()  : '',
                        'rect'     => $rect,
                        'contents' => $contentsObj instanceof PdfString ? $this->decodePdfString($contentsObj->getValue()) : '',
                    ];
                }
            }
        }

        return [
            'number'      => $pageNumber,
            'width'       => $width,
            'height'      => $height,
            'fontNames'   => $fontNames,
            'imageNames'  => $imageNames,
            'text'        => $this->extractTextFromPage($page),
            'annotations' => $annots,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    /** @return array<int, PdfObject> */
    public function getObjectCache(): array { return $this->objectCache; }

    public function getXref(): CrossReferenceTable { return $this->xref; }
}
