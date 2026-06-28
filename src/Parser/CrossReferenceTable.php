<?php

declare(strict_types=1);

namespace Papier\Parser;

/**
 * PDF cross-reference table and stream parser (ISO 32000-1 §7.5.4–§7.5.8).
 *
 * Builds an offset table mapping object number → byte offset.
 * Supports traditional xref tables, PDF 1.5+ xref streams, hybrid-reference
 * files (/XRefStm), and compressed objects stored in object streams (/ObjStm).
 */
final class CrossReferenceTable
{
    /** @var array<int, array{offset:int, generation:int, inUse:bool, compressed?:bool}> */
    private array $entries = [];
    private int   $size    = 0;
    private ?int  $root    = null; // catalog object number
    private ?int  $info    = null; // info dict object number
    private ?int  $encrypt = null;
    private ?\Papier\Objects\PdfObject $encryptRef = null;
    private ?\Papier\Objects\PdfArray  $idArray    = null;
    private string $fileId1 = '';
    private string $fileId2 = '';

    /** Byte offsets already visited, to guard against malformed /Prev cycles. */
    private array $visitedOffsets = [];

    /** Byte offset given by the file's final startxref (newest revision). */
    private ?int $startXref = null;

    public function parse(Tokenizer $tok): void
    {
        // Normal path: follow startxref through the cross-reference sections.
        $startxref = $this->findStartXRef($tok);
        if ($startxref !== null) {
            $this->startXref = $startxref;
            try {
                $this->parseXRefAt($tok, $startxref);
            } catch (\Throwable) {
                // Corrupt xref — fall through to a full rebuild.
            }
        }

        // If the cross-reference is missing or did not yield a usable catalog,
        // reconstruct it by scanning the file for objects (§ recovery).
        if ($this->root === null || !isset($this->entries[$this->root])) {
            $this->rebuild($tok);
        }

        if ($this->root === null) {
            throw new \RuntimeException('Cannot find document catalog (file is not a recoverable PDF).');
        }
    }

    /**
     * Rebuild the cross-reference table by scanning the raw bytes for
     * `N G obj` definitions and recovering the trailer/catalog.  Used when the
     * xref is absent or corrupt (a common defect in third-party PDFs).
     *
     * Note: objects stored inside object streams cannot be recovered this way.
     */
    private function rebuild(Tokenizer $tok): void
    {
        $data = $tok->getData();

        // 1. Locate every "N G obj"; later definitions win (incremental updates).
        if (preg_match_all('/(\d+)\s+(\d+)\s+obj\b/', $data, $m, PREG_OFFSET_CAPTURE)) {
            foreach ($m[1] as $i => $numMatch) {
                $offset = $numMatch[1];
                // Skip matches that are part of a longer number.
                if ($offset > 0 && ctype_digit($data[$offset - 1])) {
                    continue;
                }
                $objNum = (int) $numMatch[0];
                $this->entries[$objNum] = [
                    'offset'     => $offset,
                    'generation' => (int) $m[2][$i][0],
                    'inUse'      => true,
                ];
                $this->size = max($this->size, $objNum + 1);
            }
        }

        // 2. Recover trailer info (Root/Info/Encrypt/ID) from the last trailer.
        $tpos = strrpos($data, 'trailer');
        if ($tpos !== false) {
            $ttok = new Tokenizer($data);
            $ttok->setPosition($tpos + strlen('trailer'));
            $ttok->skipWhitespace();
            $parsed = (new ObjectParser($ttok))->parseObject();
            if ($parsed instanceof \Papier\Objects\PdfDictionary) {
                $this->extractTrailerInfo($parsed);
            }
        }

        // 3. If still no catalog, find the object that declares /Type /Catalog.
        if ($this->root === null && preg_match('/\/Type\s*\/Catalog/', $data, $cm, PREG_OFFSET_CAPTURE)) {
            $catOffset = $cm[0][1];
            $best = null;
            $bestOff = -1;
            foreach ($this->entries as $num => $entry) {
                if ($entry['offset'] <= $catOffset && $entry['offset'] > $bestOff) {
                    $bestOff = $entry['offset'];
                    $best = $num;
                }
            }
            $this->root = $best;
        }
    }

    /** Byte offset of the newest cross-reference section (for incremental updates). */
    public function getStartXref(): ?int { return $this->startXref; }

    private function findStartXRef(Tokenizer $tok): ?int
    {
        $data   = $tok->getData();
        $len    = strlen($data);
        $search = max(0, $len - 1024);
        $chunk  = substr($data, $search);
        $pos    = strrpos($chunk, 'startxref');
        if ($pos === false) {
            return null;
        }
        // Parse the number after startxref
        $afterPos = $search + $pos + strlen('startxref');
        $sub      = ltrim(substr($data, $afterPos, 32));
        if (preg_match('/^(\d+)/', $sub, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    private function parseXRefAt(Tokenizer $tok, int $offset): void
    {
        if (isset($this->visitedOffsets[$offset])) {
            return; // already parsed (or cyclic /Prev chain)
        }
        $this->visitedOffsets[$offset] = true;

        $tok->setPosition($offset);
        $tok->skipWhitespace();
        $firstToken = $tok->nextToken();

        if ($firstToken['type'] === Tokenizer::T_KEYWORD && $firstToken['value'] === 'xref') {
            $this->parseTraditionalXRef($tok);
        } elseif ($firstToken['type'] === Tokenizer::T_INTEGER) {
            // Could be an xref stream (indirect object)
            $this->parseXRefStream($tok, $offset);
        }
    }

    /**
     * Record an entry unless a newer revision already defined it.  The xref
     * chain is parsed newest-first (startxref, then /Prev), so the first writer
     * of an object number wins.
     */
    private function recordEntry(int $objNum, array $entry): void
    {
        if (!isset($this->entries[$objNum])) {
            $this->entries[$objNum] = $entry;
        }
    }

    private function parseTraditionalXRef(Tokenizer $tok): void
    {
        // Parse subsections: first_obj_num count \n (…20-byte entries…)
        while (true) {
            $tok->skipWhitespace();
            $t = $tok->nextToken();
            if ($t['type'] === Tokenizer::T_KEYWORD && $t['value'] === 'trailer') {
                break;
            }
            if ($t['type'] !== Tokenizer::T_INTEGER) {
                break;
            }
            $firstObj = $t['value'];
            $count    = $tok->nextToken()['value'];

            // Skip the line ending after count
            $tok->restOfLine();

            for ($i = 0; $i < $count; $i++) {
                $entry = $tok->readRaw(20);
                // Format: oooooooooo ggggg n|f\r\n
                $offsetStr     = substr($entry, 0, 10);
                $generationStr = substr($entry, 11, 5);
                $inUse         = ($entry[17] ?? 'f') === 'n';
                $objNum        = $firstObj + $i;
                $this->recordEntry($objNum, [
                    'offset'     => (int) $offsetStr,
                    'generation' => (int) $generationStr,
                    'inUse'      => $inUse,
                ]);
            }
        }

        // Parse trailer
        $this->parseTrailer($tok);
    }

    private function parseTrailer(Tokenizer $tok): void
    {
        $parser = new ObjectParser($tok);
        // Expect '<<' at current position
        $tok->skipWhitespace();
        $dictOrStream = $parser->parseObject();

        if ($dictOrStream instanceof \Papier\Objects\PdfDictionary) {
            $this->extractTrailerInfo($dictOrStream);

            // Hybrid-reference file (§7.5.8.4): a traditional table may also point
            // to a cross-reference stream holding entries for compressed objects.
            $xrefStm = $dictOrStream->get('XRefStm');
            if ($xrefStm instanceof \Papier\Objects\PdfInteger) {
                $savedPos = $tok->getPosition();
                $this->parseXRefAt($tok, $xrefStm->getValue());
                $tok->setPosition($savedPos);
            }

            // Check for /Prev to chain older xref sections
            $prev = $dictOrStream->get('Prev');
            if ($prev instanceof \Papier\Objects\PdfInteger) {
                $savedPos = $tok->getPosition();
                $this->parseXRefAt($tok, $prev->getValue());
                $tok->setPosition($savedPos);
            }
        }
    }

    private function parseXRefStream(Tokenizer $tok, int $offset): void
    {
        // xref stream = indirect object containing a stream
        $tok->setPosition($offset);
        $parser = new ObjectParser($tok);

        // Read: n g obj <<dict>> stream … endstream endobj
        $tok->nextToken(); // object number
        $tok->nextToken(); // generation number
        $tok->nextToken(); // 'obj'

        $stream = $parser->parseObject();
        if (!$stream instanceof \Papier\Objects\PdfStream) {
            return;
        }

        $dict = $stream->getDictionary();
        $this->extractTrailerInfo($dict);

        // Parse cross-reference stream data
        $w    = $dict->get('W');
        $index = $dict->get('Index');
        if (!$w instanceof \Papier\Objects\PdfArray) { return; }

        $widths = [];
        foreach ($w as $wv) {
            $widths[] = ($wv instanceof \Papier\Objects\PdfInteger) ? $wv->getValue() : 0;
        }

        // /Index defaults to [0 Size]; it may contain several sub-sections.
        $subsections = [];
        if ($index instanceof \Papier\Objects\PdfArray && count($index) >= 2) {
            $items = $index->getItems();
            for ($k = 0; $k + 1 < count($items); $k += 2) {
                $subsections[] = [
                    ($items[$k]     instanceof \Papier\Objects\PdfInteger) ? $items[$k]->getValue()     : 0,
                    ($items[$k + 1] instanceof \Papier\Objects\PdfInteger) ? $items[$k + 1]->getValue() : 0,
                ];
            }
        } else {
            $subsections[] = [0, $this->size ?: 0];
        }

        $data      = $stream->decode();
        $entrySize = array_sum($widths);
        $pos       = 0;

        foreach ($subsections as [$firstObj, $count]) {
            for ($i = 0; $i < $count && ($pos + $entrySize) <= strlen($data); $i++) {
                $fields = [];
                foreach ($widths as $w2) {
                    $val = 0;
                    for ($j = 0; $j < $w2; $j++) {
                        $val = ($val << 8) | ord($data[$pos++]);
                    }
                    $fields[] = $val;
                }

                // Default type is 1 when the first field width is 0 (§7.5.8.3).
                $type   = $widths[0] === 0 ? 1 : ($fields[0] ?? 1);
                $field2 = $fields[1] ?? 0;
                $field3 = $fields[2] ?? 0;

                $objNum = $firstObj + $i;
                $entry = match ($type) {
                    0 => ['offset' => $field2, 'generation' => $field3, 'inUse' => false],
                    1 => ['offset' => $field2, 'generation' => $field3, 'inUse' => true],
                    // Type 2: object lives inside object stream #field2 at index field3.
                    2 => ['offset' => $field2, 'generation' => $field3, 'inUse' => true, 'compressed' => true],
                    default => ['offset' => 0, 'generation' => 0, 'inUse' => false],
                };
                $this->recordEntry($objNum, $entry);
            }
        }

        // Follow /Prev to chain older xref sections.
        $prev = $dict->get('Prev');
        if ($prev instanceof \Papier\Objects\PdfInteger) {
            $this->parseXRefAt($tok, $prev->getValue());
        }
    }

    private function extractTrailerInfo(\Papier\Objects\PdfDictionary $dict): void
    {
        $size = $dict->get('Size');
        if ($size instanceof \Papier\Objects\PdfInteger) {
            $this->size = max($this->size, $size->getValue());
        }

        $root = $dict->get('Root');
        if ($root instanceof \Papier\Objects\PdfIndirectReference && $this->root === null) {
            $this->root = $root->getObjectNumber();
        }

        $info = $dict->get('Info');
        if ($info instanceof \Papier\Objects\PdfIndirectReference && $this->info === null) {
            $this->info = $info->getObjectNumber();
        }

        $encrypt = $dict->get('Encrypt');
        if ($encrypt !== null && $this->encrypt === null) {
            if ($encrypt instanceof \Papier\Objects\PdfIndirectReference) {
                $this->encrypt    = $encrypt->getObjectNumber();
                $this->encryptRef = $encrypt;
            } elseif ($encrypt instanceof \Papier\Objects\PdfDictionary) {
                // Rare: inline (direct) Encrypt dictionary.
                $this->encryptRef = $encrypt;
            }
        }

        $id = $dict->get('ID');
        if ($id instanceof \Papier\Objects\PdfArray && $this->fileId1 === '') {
            $this->idArray = $id;
            $items = $id->getItems();
            if (isset($items[0]) && $items[0] instanceof \Papier\Objects\PdfString) {
                $this->fileId1 = $items[0]->getValue();
            }
            if (isset($items[1]) && $items[1] instanceof \Papier\Objects\PdfString) {
                $this->fileId2 = $items[1]->getValue();
            }
        }
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getOffset(int $objNum): ?int
    {
        $entry = $this->entries[$objNum] ?? null;
        if ($entry === null || !$entry['inUse'] || !empty($entry['compressed'])) {
            return null;
        }
        return $entry['offset'];
    }

    /** Return the raw xref entry for an object number, or null. */
    public function getEntry(int $objNum): ?array
    {
        return $this->entries[$objNum] ?? null;
    }

    /** True when the object is stored inside an object stream (/ObjStm). */
    public function isCompressed(int $objNum): bool
    {
        return !empty($this->entries[$objNum]['compressed']);
    }

    /**
     * For a compressed object, return its container object stream number and the
     * object's 0-based index within that stream.
     *
     * @return array{stream:int, index:int}|null
     */
    public function getCompressedLocation(int $objNum): ?array
    {
        $entry = $this->entries[$objNum] ?? null;
        if ($entry === null || empty($entry['compressed'])) {
            return null;
        }
        return ['stream' => $entry['offset'], 'index' => $entry['generation']];
    }

    public function getGeneration(int $objNum): int
    {
        return $this->entries[$objNum]['generation'] ?? 0;
    }

    public function getCatalogObjectNumber(): ?int { return $this->root; }
    public function getInfoObjectNumber(): ?int    { return $this->info; }
    public function getEncryptObjectNumber(): ?int { return $this->encrypt; }
    public function getEncryptReference(): ?\Papier\Objects\PdfObject { return $this->encryptRef; }
    public function getIdArray(): ?\Papier\Objects\PdfArray { return $this->idArray; }
    public function getFileId1(): string           { return $this->fileId1; }
    public function getFileId2(): string           { return $this->fileId2; }
    public function getSize(): int                 { return $this->size; }

    /** @return array<int, array{offset:int, generation:int, inUse:bool, compressed?:bool}> */
    public function getEntries(): array { return $this->entries; }
}
