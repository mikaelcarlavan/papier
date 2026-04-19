<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Name object (ISO 32000-1 §7.3.5).
 *
 * An atomic symbol uniquely defined by its sequence of characters.
 * Characters outside the printable ASCII range, or the `#` character, are
 * represented using the `#xx` escape notation.
 */
final class PdfName extends PdfObject
{
    /** Characters that MUST be escaped in a name (§7.3.5 Table 4). */
    private const MUST_ESCAPE = [
        "\x00", "\t", "\n", "\x0C", "\r", ' ',
        '(', ')', '<', '>', '[', ']', '{', '}', '/', '%', '#',
    ];

    public function __construct(private readonly string $value) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        $result = '/';
        $len    = strlen($this->value);
        for ($i = 0; $i < $len; $i++) {
            $c   = $this->value[$i];
            $ord = ord($c);
            if ($ord < 0x21 || $ord > 0x7E || in_array($c, self::MUST_ESCAPE, true)) {
                $result .= sprintf('#%02X', $ord);
            } else {
                $result .= $c;
            }
        }
        return $result;
    }
}
