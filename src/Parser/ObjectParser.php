<?php

declare(strict_types=1);

namespace Papier\Parser;

use Papier\Objects\{
    PdfArray, PdfBoolean, PdfDictionary, PdfIndirectReference,
    PdfInteger, PdfName, PdfNull, PdfObject, PdfReal, PdfStream, PdfString
};

/**
 * Parses PDF objects from a tokenizer (ISO 32000-1 §7.3).
 */
final class ObjectParser
{
    public function __construct(private readonly Tokenizer $tokenizer) {}

    /**
     * Parse the next complete PDF object from the current position.
     */
    public function parseObject(): PdfObject
    {
        $token = $this->tokenizer->nextToken();

        return match ($token['type']) {
            Tokenizer::T_INTEGER => $this->parseIntegerOrRef($token['value']),
            Tokenizer::T_REAL    => new PdfReal($token['value']),
            Tokenizer::T_STRING  => new PdfString($token['value']),
            Tokenizer::T_HEXSTR  => PdfString::hex($token['value']),
            Tokenizer::T_NAME    => new PdfName($token['value']),
            Tokenizer::T_KEYWORD => match ($token['value']) {
                'true'  => new PdfBoolean(true),
                'false' => new PdfBoolean(false),
                'null'  => new PdfNull(),
                default => new PdfName($token['value']), // fallback
            },
            Tokenizer::T_ARRAY_OPEN => $this->parseArray(),
            Tokenizer::T_DICT_OPEN  => $this->parseDictionaryOrStream(),
            default => new PdfNull(),
        };
    }

    /**
     * An integer token may be the first of an indirect reference `n g R`.
     * Peek ahead to determine.
     */
    private function parseIntegerOrRef(int $value): PdfObject
    {
        $savedPos = $this->tokenizer->getPosition();

        $this->tokenizer->skipWhitespace();
        $t2 = $this->tokenizer->nextToken();
        if ($t2['type'] !== Tokenizer::T_INTEGER) {
            $this->tokenizer->setPosition($savedPos);
            return new PdfInteger($value);
        }

        $this->tokenizer->skipWhitespace();
        $t3 = $this->tokenizer->nextToken();
        if ($t3['type'] === Tokenizer::T_KEYWORD && $t3['value'] === 'R') {
            return new PdfIndirectReference($value, $t2['value']);
        }

        $this->tokenizer->setPosition($savedPos);
        return new PdfInteger($value);
    }

    /** Parse a PDF array `[ … ]`. */
    private function parseArray(): PdfArray
    {
        $arr = new PdfArray();
        while (true) {
            $this->tokenizer->skipWhitespace();
            // Save the exact position so a non-terminator token can be re-read in
            // full by parseObject (token widths are not generally reconstructable).
            $savedPos = $this->tokenizer->getPosition();
            $peek = $this->tokenizer->nextToken();
            if ($peek['type'] === Tokenizer::T_ARRAY_CLOSE || $peek['type'] === Tokenizer::T_EOF) {
                break;
            }
            $this->tokenizer->setPosition($savedPos);
            $arr->add($this->parseObject());
            // Defend against malformed input that fails to advance the cursor.
            if ($this->tokenizer->getPosition() <= $savedPos) {
                break;
            }
        }
        return $arr;
    }

    /** Parse a PDF dictionary `<< … >>`, which may be followed by a stream. */
    private function parseDictionaryOrStream(): PdfDictionary|PdfStream
    {
        $dict = new PdfDictionary();

        while (true) {
            $this->tokenizer->skipWhitespace();
            $before = $this->tokenizer->getPosition();
            $key = $this->tokenizer->nextToken();
            if ($key['type'] === Tokenizer::T_DICT_CLOSE || $key['type'] === Tokenizer::T_EOF) {
                break;
            }
            if ($key['type'] !== Tokenizer::T_NAME) {
                // Malformed key; skip it but never stall on zero-width input.
                if ($this->tokenizer->getPosition() <= $before) {
                    break;
                }
                continue;
            }
            $value = $this->parseObject();
            $dict->set($key['value'], $value);
        }

        // Check for `stream` keyword
        $savedPos = $this->tokenizer->getPosition();
        $this->tokenizer->skipWhitespace();
        $tok = $this->tokenizer->nextToken();

        if ($tok['type'] === Tokenizer::T_KEYWORD && $tok['value'] === 'stream') {
            // Skip \r\n or \n
            $c = $this->tokenizer->peek();
            if ($c === "\r") { $this->tokenizer->read(); }
            $c = $this->tokenizer->peek();
            if ($c === "\n") { $this->tokenizer->read(); }

            $start     = $this->tokenizer->getPosition();
            $lengthObj = $dict->get('Length');
            $data      = null;

            // Trust a direct /Length only when 'endstream' actually follows it;
            // otherwise (indirect or wrong /Length) locate 'endstream' by scanning.
            if ($lengthObj instanceof PdfInteger) {
                $len  = $lengthObj->getValue();
                $tail = substr($this->tokenizer->getData(), $start + $len, 20);
                if (preg_match('/^\s*endstream/', $tail)) {
                    $data = $this->tokenizer->readRaw($len);
                }
            }

            if ($data === null) {
                $all = $this->tokenizer->getData();
                $end = strpos($all, 'endstream', $start);
                if ($end === false) {
                    $data = substr($all, $start);
                    $this->tokenizer->setPosition(strlen($all));
                } else {
                    $raw = substr($all, $start, $end - $start);
                    // Drop the single EOL that precedes 'endstream'.
                    $raw = preg_replace('/\r\n$|\r$|\n$/', '', $raw);
                    $data = $raw;
                    $this->tokenizer->setPosition($end);
                }
            }

            // Skip `endstream`
            $this->tokenizer->skipWhitespace();
            $this->tokenizer->nextToken(); // endstream

            $stream = new PdfStream($dict);
            $stream->setData($data);
            return $stream;
        }

        $this->tokenizer->setPosition($savedPos);
        return $dict;
    }
}
