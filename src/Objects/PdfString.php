<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF String object (ISO 32000-1 §7.3.4).
 *
 * Supports both literal strings `(…)` and hexadecimal strings `<…>`.
 * PDFDocEncoding or UTF-16BE (with BOM) should be used for text strings.
 */
final class PdfString extends PdfObject
{
    public function __construct(
        private readonly string $value,
        private readonly bool   $hex = false,
    ) {}

    /** Create a literal string. */
    public static function literal(string $value): self
    {
        return new self($value, false);
    }

    /** Create a hexadecimal string. */
    public static function hex(string $value): self
    {
        return new self($value, true);
    }

    /**
     * Create a text string encoded as UTF-16BE with BOM.
     * Use this for user-visible text (titles, author names, etc.).
     */
    public static function text(string $utf8Value): self
    {
        $utf16be = "\xFE\xFF" . mb_convert_encoding($utf8Value, 'UTF-16BE', 'UTF-8');
        return new self($utf16be, false);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isHex(): bool
    {
        return $this->hex;
    }

    public function toString(): string
    {
        if ($this->hex) {
            return '<' . bin2hex($this->value) . '>';
        }

        // Escape special characters inside a literal string.
        $escaped = '';
        $len     = strlen($this->value);
        for ($i = 0; $i < $len; $i++) {
            $c   = $this->value[$i];
            $ord = ord($c);
            $escaped .= match ($c) {
                '('    => '\\(',
                ')'    => '\\)',
                '\\'   => '\\\\',
                "\r"   => '\\r',
                "\n"   => '\\n',
                "\t"   => '\\t',
                "\x08" => '\\b',
                "\x0C" => '\\f',
                default => ($ord < 0x20 || $ord > 0x7E) ? sprintf('\\%03o', $ord) : $c,
            };
        }
        return "($escaped)";
    }
}
