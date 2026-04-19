<?php

declare(strict_types=1);

namespace Papier\Parser;

/**
 * PDF tokenizer (ISO 32000-1 §7.2).
 *
 * Converts a raw byte sequence into a stream of PDF tokens.
 * Handles comments, whitespace, delimiters, keywords, and literal/hex strings.
 */
final class Tokenizer
{
    private int    $pos   = 0;
    private int    $len;
    private string $data;

    // Token types
    public const T_KEYWORD  = 'keyword';
    public const T_INTEGER  = 'integer';
    public const T_REAL     = 'real';
    public const T_STRING   = 'string';
    public const T_HEXSTR   = 'hexstring';
    public const T_NAME     = 'name';
    public const T_ARRAY_OPEN  = '[';
    public const T_ARRAY_CLOSE = ']';
    public const T_DICT_OPEN   = '<<';
    public const T_DICT_CLOSE  = '>>';
    public const T_EOF      = 'eof';

    private const WHITESPACE  = ["\x00", "\x09", "\x0A", "\x0C", "\x0D", "\x20"];
    private const DELIMITERS  = ['(', ')', '<', '>', '[', ']', '{', '}', '/', '%'];

    public function __construct(string $data)
    {
        $this->data = $data;
        $this->len  = strlen($data);
    }

    public function getPosition(): int { return $this->pos; }
    public function setPosition(int $pos): void { $this->pos = $pos; }

    public function getLength(): int { return $this->len; }

    /** Read a single byte without consuming it. */
    public function peek(): ?string
    {
        return ($this->pos < $this->len) ? $this->data[$this->pos] : null;
    }

    /** Read a single byte and advance. */
    public function read(): ?string
    {
        return ($this->pos < $this->len) ? $this->data[$this->pos++] : null;
    }

    /** Read $n bytes. */
    public function readBytes(int $n): string
    {
        $bytes     = substr($this->data, $this->pos, $n);
        $this->pos += $n;
        return $bytes;
    }

    /** Skip whitespace and comments. */
    public function skipWhitespace(): void
    {
        while ($this->pos < $this->len) {
            $c = $this->data[$this->pos];
            if ($c === '%') {
                // Comment: skip to end of line
                while ($this->pos < $this->len && $this->data[$this->pos] !== "\n" && $this->data[$this->pos] !== "\r") {
                    $this->pos++;
                }
            } elseif (in_array($c, self::WHITESPACE, true)) {
                $this->pos++;
            } else {
                break;
            }
        }
    }

    /**
     * Read the next token from the stream.
     *
     * @return array{type: string, value: mixed}
     */
    public function nextToken(): array
    {
        $this->skipWhitespace();

        if ($this->pos >= $this->len) {
            return ['type' => self::T_EOF, 'value' => null];
        }

        $c = $this->data[$this->pos];

        // Literal string
        if ($c === '(') {
            return ['type' => self::T_STRING, 'value' => $this->readLiteralString()];
        }

        // Hex string or dictionary delimiter
        if ($c === '<') {
            if (($this->pos + 1 < $this->len) && $this->data[$this->pos + 1] === '<') {
                $this->pos += 2;
                return ['type' => self::T_DICT_OPEN, 'value' => '<<'];
            }
            return ['type' => self::T_HEXSTR, 'value' => $this->readHexString()];
        }

        // Dictionary close or >
        if ($c === '>') {
            if (($this->pos + 1 < $this->len) && $this->data[$this->pos + 1] === '>') {
                $this->pos += 2;
                return ['type' => self::T_DICT_CLOSE, 'value' => '>>'];
            }
            $this->pos++;
            return ['type' => self::T_KEYWORD, 'value' => '>'];
        }

        // Array delimiters
        if ($c === '[') { $this->pos++; return ['type' => self::T_ARRAY_OPEN,  'value' => '[']; }
        if ($c === ']') { $this->pos++; return ['type' => self::T_ARRAY_CLOSE, 'value' => ']']; }

        // Name object
        if ($c === '/') {
            return ['type' => self::T_NAME, 'value' => $this->readName()];
        }

        // Number or keyword
        $token = $this->readToken();
        if (is_numeric($token) && !str_contains($token, '.')) {
            return ['type' => self::T_INTEGER, 'value' => (int) $token];
        }
        if (is_numeric($token)) {
            return ['type' => self::T_REAL, 'value' => (float) $token];
        }
        return ['type' => self::T_KEYWORD, 'value' => $token];
    }

    /** Read a regular token (keyword or number) until whitespace/delimiter. */
    private function readToken(): string
    {
        $start = $this->pos;
        while ($this->pos < $this->len) {
            $c = $this->data[$this->pos];
            if (in_array($c, self::WHITESPACE, true) || in_array($c, self::DELIMITERS, true)) {
                break;
            }
            $this->pos++;
        }
        return substr($this->data, $start, $this->pos - $start);
    }

    /** Read a PDF name object (starts after '/'). */
    private function readName(): string
    {
        $this->pos++; // skip '/'
        $name = '';
        while ($this->pos < $this->len) {
            $c = $this->data[$this->pos];
            if (in_array($c, self::WHITESPACE, true) || in_array($c, self::DELIMITERS, true)) {
                break;
            }
            if ($c === '#') {
                // Hex escape
                $hex   = substr($this->data, $this->pos + 1, 2);
                $name .= chr(hexdec($hex));
                $this->pos += 3;
            } else {
                $name .= $c;
                $this->pos++;
            }
        }
        return $name;
    }

    /** Read a PDF literal string `(…)`. */
    private function readLiteralString(): string
    {
        $this->pos++; // skip '('
        $str    = '';
        $depth  = 1;
        while ($this->pos < $this->len) {
            $c = $this->data[$this->pos++];
            if ($c === '\\') {
                $next = $this->data[$this->pos++] ?? '';
                $str .= match ($next) {
                    'n'  => "\n",
                    'r'  => "\r",
                    't'  => "\t",
                    'b'  => "\x08",
                    'f'  => "\x0C",
                    '('  => '(',
                    ')'  => ')',
                    '\\' => '\\',
                    "\n", "\r" => '', // line continuation
                    default => (ctype_digit($next) ? $this->readOctal($next) : $next),
                };
            } elseif ($c === '(') {
                $depth++;
                $str .= $c;
            } elseif ($c === ')') {
                $depth--;
                if ($depth === 0) { break; }
                $str .= $c;
            } else {
                $str .= $c;
            }
        }
        return $str;
    }

    private function readOctal(string $first): string
    {
        $oct = $first;
        for ($i = 0; $i < 2 && $this->pos < $this->len && ctype_digit($this->data[$this->pos]); $i++) {
            $oct .= $this->data[$this->pos++];
        }
        return chr(octdec($oct));
    }

    /** Read a PDF hexadecimal string `<…>`. */
    private function readHexString(): string
    {
        $this->pos++; // skip '<'
        $hex = '';
        while ($this->pos < $this->len) {
            $c = $this->data[$this->pos++];
            if ($c === '>') { break; }
            if (ctype_xdigit($c)) { $hex .= $c; }
        }
        if (strlen($hex) % 2 !== 0) { $hex .= '0'; }
        return hex2bin($hex) ?: '';
    }

    /** Peek at the rest of the line (for stream keyword detection). */
    public function restOfLine(): string
    {
        $start = $this->pos;
        while ($this->pos < $this->len && $this->data[$this->pos] !== "\n") {
            $this->pos++;
        }
        if ($this->pos < $this->len) { $this->pos++; } // skip \n
        return substr($this->data, $start, $this->pos - $start);
    }

    /** Read $n raw bytes from current position. */
    public function readRaw(int $n): string
    {
        $bytes     = substr($this->data, $this->pos, $n);
        $this->pos += $n;
        return $bytes;
    }

    /** Search backward from $startPos for a string, returning its position. */
    public function findBackward(string $needle, int $startPos, int $maxSearch = 1024): ?int
    {
        $searchFrom = max(0, $startPos - $maxSearch);
        $chunk      = substr($this->data, $searchFrom, $startPos - $searchFrom);
        $pos        = strrpos($chunk, $needle);
        return ($pos !== false) ? ($searchFrom + $pos) : null;
    }

    public function getData(): string { return $this->data; }
}
