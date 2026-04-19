<?php

declare(strict_types=1);

namespace Papier\Parser;

/**
 * PDF cross-reference table and stream parser (ISO 32000-1 §7.5.4–§7.5.8).
 *
 * Builds an offset table mapping object number → byte offset.
 * Supports traditional xref tables and PDF 1.5+ xref streams.
 */
final class CrossReferenceTable
{
    /** @var array<int, array{offset: int, generation: int, inUse: bool}> */
    private array $entries = [];
    private int   $size    = 0;
    private ?int  $root    = null; // catalog object number
    private ?int  $info    = null; // info dict object number
    private ?int  $encrypt = null;
    private string $fileId1 = '';
    private string $fileId2 = '';

    public function parse(Tokenizer $tok): void
    {
        // Find startxref from the end of the file
        $startxref = $this->findStartXRef($tok);
        if ($startxref === null) {
            throw new \RuntimeException('Cannot find startxref.');
        }

        $this->parseXRefAt($tok, $startxref);
    }

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
                $this->entries[$objNum] = [
                    'offset'     => (int) $offsetStr,
                    'generation' => (int) $generationStr,
                    'inUse'      => $inUse,
                ];
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

            // Check for /Prev to chain xref tables
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

        $firstObj = 0;
        $count    = $this->size ?: 0;
        if ($index instanceof \Papier\Objects\PdfArray && count($index) >= 2) {
            $items     = $index->getItems();
            $firstObj  = ($items[0] instanceof \Papier\Objects\PdfInteger) ? $items[0]->getValue() : 0;
            $count     = ($items[1] instanceof \Papier\Objects\PdfInteger) ? $items[1]->getValue() : 0;
        }

        $data      = $stream->decode();
        $entrySize = array_sum($widths);
        $pos       = 0;

        for ($i = 0; $i < $count && ($pos + $entrySize) <= strlen($data); $i++) {
            $fields = [];
            foreach ($widths as $w2) {
                $val = 0;
                for ($j = 0; $j < $w2; $j++) {
                    $val = ($val << 8) | ord($data[$pos++]);
                }
                $fields[] = $val;
            }

            $type   = $fields[0] ?? 1;
            $field2 = $fields[1] ?? 0;
            $field3 = $fields[2] ?? 0;

            $objNum = $firstObj + $i;
            $this->entries[$objNum] = match ($type) {
                0 => ['offset' => $field2, 'generation' => $field3, 'inUse' => false],
                1 => ['offset' => $field2, 'generation' => $field3, 'inUse' => true],
                2 => ['offset' => $field2, 'generation' => $field3, 'inUse' => true, 'compressed' => true],
                default => ['offset' => 0, 'generation' => 0, 'inUse' => false],
            };
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
        if ($encrypt instanceof \Papier\Objects\PdfIndirectReference && $this->encrypt === null) {
            $this->encrypt = $encrypt->getObjectNumber();
        }

        $id = $dict->get('ID');
        if ($id instanceof \Papier\Objects\PdfArray && $this->fileId1 === '') {
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
        return ($entry && $entry['inUse']) ? $entry['offset'] : null;
    }

    public function getCatalogObjectNumber(): ?int { return $this->root; }
    public function getInfoObjectNumber(): ?int    { return $this->info; }
    public function getEncryptObjectNumber(): ?int { return $this->encrypt; }
    public function getFileId1(): string           { return $this->fileId1; }

    /** @return array<int, array{offset:int, generation:int, inUse:bool}> */
    public function getEntries(): array { return $this->entries; }
}
