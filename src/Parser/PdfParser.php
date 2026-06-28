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

    /** @var array<int, array<int, PdfObject>> Parsed object-stream members, keyed by container object number */
    private array $objStmCache = [];

    private ?PdfDictionary $catalog = null;
    private ?PdfDictionary $info    = null;

    /** @var PdfDictionary[]|null Memoised flattened page list */
    private ?array $pagesCache = null;

    /** Password supplied to open an encrypted document. */
    private string $password = '';

    /** Decryptor for an encrypted document, or null when not encrypted. */
    private ?\Papier\Encryption\StandardDecryptor $decryptor = null;

    public function __construct(private readonly string $data)
    {
        $this->tok       = new Tokenizer($data);
        $this->xref      = new CrossReferenceTable();
        $this->objParser = new ObjectParser($this->tok);
    }

    /**
     * Supply the password used to open an encrypted document.  Call before
     * {@see parse()}.  The empty string is tried automatically.
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
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

        // Set up decryption before resolving any other object, since strings and
        // streams (including the catalog's) are encrypted (§7.6).
        $this->setupDecryption();

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

    // ── Encryption ────────────────────────────────────────────────────────────

    /**
     * If the document declares an /Encrypt dictionary, build a decryptor from
     * the supplied password and the file ID.
     *
     * @throws \RuntimeException  If the password is incorrect.
     */
    private function setupDecryption(): void
    {
        $encRef = $this->xref->getEncryptReference();
        if ($encRef === null) {
            return;
        }

        // Resolve the Encrypt dictionary itself WITHOUT decryption.
        $encDict = $encRef instanceof PdfDictionary
            ? $encRef
            : $this->resolve($encRef);
        if (!$encDict instanceof PdfDictionary) {
            return;
        }

        // The first element of /ID is part of the key derivation for V<5.
        $id1 = '';
        $idArr = $this->xref->getIdArray();
        if ($idArr instanceof PdfArray) {
            $first = $idArr->get(0);
            if ($first instanceof PdfString) {
                $id1 = $first->getValue();
            }
        }

        $this->decryptor = \Papier\Encryption\StandardDecryptor::fromDictionary(
            $encDict,
            $this->password,
            $id1,
        );
    }

    /** True when the source document is encrypted. */
    public function isEncrypted(): bool
    {
        return $this->xref->getEncryptReference() !== null;
    }

    /** Recursively decrypt strings and stream data within a freshly-parsed object. */
    private function decryptObject(PdfObject $obj, int $objNum, int $genNum): void
    {
        if ($this->decryptor === null) {
            return;
        }
        if ($obj instanceof PdfStream) {
            // Cross-reference and object streams are never encrypted (§7.5.8.2, §7.6.2).
            $type = $obj->getDictionary()->get('Type');
            $typeName = $type instanceof PdfName ? $type->getValue() : '';
            if ($typeName !== 'XRef') {
                $obj->setData($this->decryptor->decrypt($obj->getData(), $objNum, $genNum));
            }
            $this->decryptObject($obj->getDictionary(), $objNum, $genNum);
        } elseif ($obj instanceof PdfDictionary) {
            foreach ($obj->getEntries() as $key => $value) {
                if ($value instanceof PdfString) {
                    $obj->set($key, new PdfString($this->decryptor->decrypt($value->getValue(), $objNum, $genNum)));
                } elseif ($value instanceof PdfArray || $value instanceof PdfDictionary) {
                    $this->decryptObject($value, $objNum, $genNum);
                }
            }
        } elseif ($obj instanceof PdfArray) {
            foreach ($obj->getItems() as $i => $value) {
                if ($value instanceof PdfString) {
                    $obj->set($i, new PdfString($this->decryptor->decrypt($value->getValue(), $objNum, $genNum)));
                } elseif ($value instanceof PdfArray || $value instanceof PdfDictionary) {
                    $this->decryptObject($value, $objNum, $genNum);
                }
            }
        }
    }

    // ── Object resolution ─────────────────────────────────────────────────────

    /**
     * Resolve an indirect object by number, following the xref.
     *
     * Handles both directly-stored objects (byte offset) and objects packed
     * inside an object stream (/ObjStm, §7.5.7).
     */
    public function resolveObject(int $objNum, int $genNum = 0): ?PdfObject
    {
        if (isset($this->objectCache[$objNum])) {
            return $this->objectCache[$objNum];
        }

        // Object stored inside a compressed object stream.
        if ($this->xref->isCompressed($objNum)) {
            $obj = $this->resolveCompressedObject($objNum);
            if ($obj !== null) {
                $this->objectCache[$objNum] = $obj;
            }
            return $obj;
        }

        $offset = $this->xref->getOffset($objNum);
        if ($offset === null) {
            return null;
        }

        $gen = $this->xref->getGeneration($objNum) ?: $genNum;

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
        $obj->setObjectNumber($objNum, $gen);

        $this->objectCache[$objNum] = $obj;

        // Decrypt strings/streams if the document is encrypted.
        if ($this->decryptor !== null && $objNum !== $this->xref->getEncryptObjectNumber()) {
            $this->decryptObject($obj, $objNum, $gen);
        }
        return $obj;
    }

    /**
     * Resolve an object packed inside an object stream (/ObjStm).  The container
     * stream is parsed once and all of its members are cached.
     */
    private function resolveCompressedObject(int $objNum): ?PdfObject
    {
        $loc = $this->xref->getCompressedLocation($objNum);
        if ($loc === null) {
            return null;
        }
        $members = $this->getObjectStreamMembers($loc['stream']);
        return $members[$objNum] ?? null;
    }

    /**
     * Parse an object stream and return all member objects keyed by object number.
     * Results are memoised per container stream.
     *
     * @return array<int, PdfObject>
     */
    private function getObjectStreamMembers(int $streamObjNum): array
    {
        if (isset($this->objStmCache[$streamObjNum])) {
            return $this->objStmCache[$streamObjNum];
        }
        $this->objStmCache[$streamObjNum] = []; // guard against recursion

        $stmObj = $this->resolveObject($streamObjNum);
        if (!$stmObj instanceof PdfStream) {
            return [];
        }

        $dict = $stmObj->getDictionary();
        $nObj     = $this->resolve($dict->get('N')     ?? new PdfNull());
        $firstObj = $this->resolve($dict->get('First') ?? new PdfNull());
        $n     = $nObj     instanceof PdfInteger ? $nObj->getValue()     : 0;
        $first = $firstObj instanceof PdfInteger ? $firstObj->getValue() : 0;
        if ($n <= 0) {
            return [];
        }

        $data = $stmObj->decode();

        // Header: N pairs of "objNum offset" integers, offsets relative to /First.
        $headerTok = new Tokenizer(substr($data, 0, $first));
        $pairs = [];
        for ($i = 0; $i < $n; $i++) {
            $num = $headerTok->nextToken();
            $off = $headerTok->nextToken();
            if ($num['type'] !== Tokenizer::T_INTEGER || $off['type'] !== Tokenizer::T_INTEGER) {
                break;
            }
            $pairs[] = [$num['value'], $off['value']];
        }

        $members = [];
        foreach ($pairs as $idx => [$num, $off]) {
            $start  = $first + $off;
            $end    = isset($pairs[$idx + 1]) ? $first + $pairs[$idx + 1][1] : strlen($data);
            $chunk  = substr($data, $start, $end - $start);
            $tok    = new Tokenizer($chunk);
            $parser = new ObjectParser($tok);
            $obj    = $parser->parseObject();
            $obj->setObjectNumber($num, 0);
            $members[$num] = $obj;
        }

        $this->objStmCache[$streamObjNum] = $members;
        return $members;
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
        if ($this->pagesCache !== null) {
            return $this->pagesCache;
        }
        if ($this->catalog === null) { return []; }

        $pagesRef = $this->catalog->get('Pages');
        if ($pagesRef === null) { return []; }

        $pagesObj = $this->resolve($pagesRef);
        if (!$pagesObj instanceof PdfDictionary) { return []; }

        return $this->pagesCache = $this->collectPages($pagesObj);
    }

    /** @return PdfDictionary[] */
    private function collectPages(PdfDictionary $node, array &$seen = [], int $depth = 0): array
    {
        // Guard against malformed/cyclic page trees.
        $id = spl_object_id($node);
        if ($depth > 100 || isset($seen[$id])) {
            return [];
        }
        $seen[$id] = true;

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
                array_push($pages, ...$this->collectPages($kid, $seen, $depth + 1));
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
        // Layout- and encoding-aware extraction (handles Type0/CJK/subset fonts
        // via embedded ToUnicode CMaps, and infers spaces/line breaks).
        $text = (new TextExtractor($this))->extractPage($page);
        if (trim($text) !== '') {
            return $text;
        }

        // Fallback: naive operand scan for content the walker couldn't decode.
        $contentRef = $page->get('Contents');
        if ($contentRef === null) { return ''; }
        $contentObjs = $contentRef instanceof PdfArray
            ? array_map(fn($r) => $this->resolve($r), $contentRef->getItems())
            : [$this->resolve($contentRef)];

        $fallback = '';
        foreach ($contentObjs as $obj) {
            if ($obj instanceof PdfStream) {
                $fallback .= $this->parseContentStreamForText($obj->decode());
            }
        }
        return $fallback;
    }

    /** Extract text strings from a raw content stream. */
    private function parseContentStreamForText(string $stream): string
    {
        $text = '';
        // Match Tj, TJ, ', " operators and their string operands.  No /u flag:
        // simple-font content streams hold single-byte (e.g. WinAnsi) text, which
        // is not valid UTF-8 and would make a /u pattern fail to match.
        if (preg_match_all('/\(([^)]*)\)\s*(?:Tj|\'|"[^"]*")/', $stream, $matches)) {
            foreach ($matches[1] as $s) {
                $text .= $this->unescapeString($s) . ' ';
            }
        }
        // TJ: [(string) …] TJ
        if (preg_match_all('/\[([^\]]*)\]\s*TJ/', $stream, $matches)) {
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
        $decoded = stripcslashes(str_replace(
            ['\\(', '\\)', '\\\\'],
            ['(', ')', '\\'],
            $s
        ));
        // Simple fonts emit WinAnsi (Windows-1252) bytes; promote to UTF-8 so
        // accented Latin text is returned correctly.  Leave already-valid UTF-8
        // (and Type0/2-byte streams) untouched.
        if ($decoded !== '' && !mb_check_encoding($decoded, 'UTF-8')) {
            $converted = @mb_convert_encoding($decoded, 'UTF-8', 'Windows-1252');
            if ($converted !== false) {
                return $converted;
            }
        }
        return $decoded;
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

    // ── Outlines (bookmarks) ──────────────────────────────────────────────────

    /**
     * Read the document outline (bookmark) tree.
     *
     * Each node has:
     *   - `title`    — bookmark title (string)
     *   - `dest`     — raw `/Dest` value as a string summary, or `''`
     *   - `children` — nested array of the same shape
     *
     * @return array<int, array{title:string,dest:string,children:array}>
     */
    public function getOutlines(): array
    {
        if ($this->catalog === null) { return []; }
        $outlinesRef = $this->catalog->get('Outlines');
        if ($outlinesRef === null) { return []; }
        $outlines = $this->resolve($outlinesRef);
        if (!$outlines instanceof PdfDictionary) { return []; }

        $first = $outlines->get('First');
        return $first === null ? [] : $this->readOutlineSiblings($first);
    }

    /** @return array<int, array{title:string,dest:string,children:array}> */
    private function readOutlineSiblings(PdfObject $firstRef, int $depth = 0): array
    {
        $items = [];
        $ref   = $firstRef;
        $guard = 0;
        while ($ref !== null && $guard++ < 10000) {
            $node = $this->resolve($ref);
            if (!$node instanceof PdfDictionary) { break; }

            $titleObj = $node->get('Title');
            $children = [];
            if ($depth < 64) {
                $childFirst = $node->get('First');
                if ($childFirst !== null) {
                    $children = $this->readOutlineSiblings($childFirst, $depth + 1);
                }
            }

            $items[] = [
                'title'    => $titleObj instanceof PdfString ? $this->decodePdfString($titleObj->getValue()) : '',
                'dest'     => $this->destSummary($node),
                'children' => $children,
            ];

            $ref = $node->get('Next');
        }
        return $items;
    }

    private function destSummary(PdfDictionary $node): string
    {
        $dest = $node->get('Dest');
        if ($dest instanceof PdfString) { return $this->decodePdfString($dest->getValue()); }
        if ($dest instanceof PdfName)   { return $dest->getValue(); }
        if ($dest instanceof PdfArray)  { return trim($dest->toString()); }
        // Fall back to an action's destination.
        $a = $node->get('A');
        if ($a !== null) {
            $aObj = $this->resolve($a);
            if ($aObj instanceof PdfDictionary) {
                $d = $aObj->get('D');
                if ($d instanceof PdfString) { return $this->decodePdfString($d->getValue()); }
                if ($d instanceof PdfArray)  { return trim($d->toString()); }
            }
        }
        return '';
    }

    // ── AcroForm fields ───────────────────────────────────────────────────────

    /**
     * Read interactive form field values from the document's /AcroForm.
     *
     * Returns the terminal (leaf) fields.  Each entry has:
     *   - `name`  — fully-qualified field name (string)
     *   - `type`  — field type: `Tx`, `Btn`, `Ch`, `Sig`, or `''`
     *   - `value` — `/V` value as a string (empty if unset)
     *
     * @return array<int, array{name:string,type:string,value:string}>
     */
    public function getFormFields(): array
    {
        if ($this->catalog === null) { return []; }
        $acroRef = $this->catalog->get('AcroForm');
        if ($acroRef === null) { return []; }
        $acro = $this->resolve($acroRef);
        if (!$acro instanceof PdfDictionary) { return []; }

        $fieldsRef = $acro->get('Fields');
        if (!$fieldsRef instanceof PdfArray) {
            $fieldsRef = $this->resolve($fieldsRef ?? new PdfNull());
        }
        if (!$fieldsRef instanceof PdfArray) { return []; }

        $out = [];
        foreach ($fieldsRef as $fref) {
            $this->collectFormField($this->resolve($fref), '', '', $out);
        }
        return $out;
    }

    /**
     * @param array<int, array{name:string,type:string,value:string}> $out
     */
    private function collectFormField(PdfObject $field, string $parentName, string $inheritedType, array &$out): void
    {
        if (!$field instanceof PdfDictionary) { return; }

        $partial = $field->get('T');
        $name = $partial instanceof PdfString ? $this->decodePdfString($partial->getValue()) : '';
        $fqName = $parentName === '' ? $name : ($name === '' ? $parentName : "$parentName.$name");

        $ftObj = $field->get('FT');
        $type  = $ftObj instanceof PdfName ? $ftObj->getValue() : $inheritedType;

        $kids = $field->get('Kids');
        $kidsArr = $kids instanceof PdfArray ? $kids : null;

        // A node with kids that are themselves fields (have /T) is non-terminal.
        $hasFieldKids = false;
        if ($kidsArr !== null) {
            foreach ($kidsArr as $kref) {
                $kid = $this->resolve($kref);
                if ($kid instanceof PdfDictionary && $kid->get('T') !== null) {
                    $hasFieldKids = true;
                    break;
                }
            }
        }

        if ($hasFieldKids) {
            foreach ($kidsArr as $kref) {
                $this->collectFormField($this->resolve($kref), $fqName, $type, $out);
            }
            return;
        }

        $out[] = [
            'name'  => $fqName,
            'type'  => $type,
            'value' => $this->fieldValueToString($field->get('V')),
        ];
    }

    private function fieldValueToString(?PdfObject $v): string
    {
        if ($v === null) { return ''; }
        $v = $this->resolve($v);
        if ($v instanceof PdfString) { return $this->decodePdfString($v->getValue()); }
        if ($v instanceof PdfName)   { return $v->getValue(); }
        return '';
    }

    // ── Embedded file attachments ───────────────────────────────────────────────

    /**
     * Read embedded file attachments from /Names /EmbeddedFiles (§7.11.4).
     *
     * Each entry has:
     *   - `name` — display file name (string)
     *   - `data` — decoded file contents (string)
     *   - `mime` — `/Subtype` MIME type, or `''`
     *
     * @return array<int, array{name:string,data:string,mime:string}>
     */
    public function getAttachments(): array
    {
        if ($this->catalog === null) { return []; }
        $namesRef = $this->catalog->get('Names');
        if ($namesRef === null) { return []; }
        $names = $this->resolve($namesRef);
        if (!$names instanceof PdfDictionary) { return []; }

        $efRef = $names->get('EmbeddedFiles');
        if ($efRef === null) { return []; }
        $ef = $this->resolve($efRef);
        if (!$ef instanceof PdfDictionary) { return []; }

        $pairs = $this->flattenNameTree($ef);
        $out = [];
        foreach ($pairs as [$key, $valueRef]) {
            $spec = $this->resolve($valueRef);
            if (!$spec instanceof PdfDictionary) { continue; }

            $display = $key;
            $fName = $spec->get('UF') ?? $spec->get('F');
            if ($fName instanceof PdfString) {
                $display = $this->decodePdfString($fName->getValue());
            }

            $efDict = $this->resolve($spec->get('EF') ?? new PdfNull());
            if (!$efDict instanceof PdfDictionary) { continue; }
            $streamRef = $efDict->get('F') ?? $efDict->get('UF');
            $stream = $this->resolve($streamRef ?? new PdfNull());
            if (!$stream instanceof PdfStream) { continue; }

            $mime = '';
            $sub  = $stream->getDictionary()->get('Subtype');
            if ($sub instanceof PdfName)   { $mime = $sub->getValue(); }
            if ($sub instanceof PdfString) { $mime = $sub->getValue(); }

            $out[] = ['name' => $display, 'data' => $stream->decode(), 'mime' => $mime];
        }
        return $out;
    }

    /**
     * Flatten a PDF name tree (§7.9.6) into [key, valueRef] pairs.
     *
     * @return array<int, array{0:string,1:PdfObject}>
     */
    private function flattenNameTree(PdfDictionary $node, int $depth = 0): array
    {
        if ($depth > 64) { return []; }
        $pairs = [];

        $namesArr = $node->get('Names');
        if ($namesArr instanceof PdfArray) {
            $items = $namesArr->getItems();
            for ($i = 0; $i + 1 < count($items); $i += 2) {
                $k = $items[$i];
                $key = $k instanceof PdfString ? $this->decodePdfString($k->getValue()) : '';
                $pairs[] = [$key, $items[$i + 1]];
            }
        }

        $kids = $node->get('Kids');
        if ($kids instanceof PdfArray) {
            foreach ($kids as $kidRef) {
                $kid = $this->resolve($kidRef);
                if ($kid instanceof PdfDictionary) {
                    $pairs = array_merge($pairs, $this->flattenNameTree($kid, $depth + 1));
                }
            }
        }
        return $pairs;
    }

    // ── Tagged PDF ────────────────────────────────────────────────────────────

    /** True when the document is marked as Tagged PDF (/MarkInfo /Marked true). */
    public function isTagged(): bool
    {
        if ($this->catalog === null) { return false; }
        $mi = $this->resolve($this->catalog->get('MarkInfo') ?? new PdfNull());
        if ($mi instanceof PdfDictionary) {
            $marked = $mi->get('Marked');
            return $marked instanceof PdfBoolean && $marked->getValue();
        }
        return false;
    }

    /** Return the structure-tree root dictionary (§14.7.2), or null. */
    public function getStructTreeRoot(): ?PdfDictionary
    {
        if ($this->catalog === null) { return null; }
        $st = $this->resolve($this->catalog->get('StructTreeRoot') ?? new PdfNull());
        return $st instanceof PdfDictionary ? $st : null;
    }

    // ── XMP metadata ────────────────────────────────────────────────────────────

    /**
     * Return the raw XMP metadata packet from the catalog's /Metadata stream
     * (§14.3.2), or `null` if the document has none.
     */
    public function getXmpMetadata(): ?string
    {
        if ($this->catalog === null) { return null; }
        $metaRef = $this->catalog->get('Metadata');
        if ($metaRef === null) { return null; }
        $meta = $this->resolve($metaRef);
        if (!$meta instanceof PdfStream) { return null; }
        return $meta->decode();
    }

    /**
     * Return all document metadata as an associative array, combining the Info
     * dictionary with high-level accessors.
     *
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return [
            'title'    => $this->getTitle(),
            'author'   => $this->getAuthor(),
            'subject'  => $this->getSubject(),
            'creator'  => $this->getCreator(),
            'keywords' => $this->getInfoString('Keywords'),
            'producer' => $this->getInfoString('Producer'),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    /** @return array<int, PdfObject> */
    public function getObjectCache(): array { return $this->objectCache; }

    public function getXref(): CrossReferenceTable { return $this->xref; }

    /** Return the original raw PDF bytes (used for incremental updates). */
    public function getRawData(): string { return $this->data; }
}
