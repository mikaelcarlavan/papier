<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Real object (ISO 32000-1 §7.3.3).
 *
 * An approximation to a mathematical real number, written as an optional sign,
 * integer part, decimal point, and fractional part.  Exponential notation is
 * NOT used because many PDF viewers do not support it.
 */
final class PdfReal extends PdfObject
{
    public function __construct(private readonly float $value) {}

    public function getValue(): float
    {
        return $this->value;
    }

    public function toString(): string
    {
        // Up to 6 decimal places; strip trailing zeros but keep at least one
        // digit after the decimal point so it is unambiguously a real.
        $s = rtrim(number_format($this->value, 6, '.', ''), '0');
        if (str_ends_with($s, '.')) {
            $s .= '0';
        }
        return $s;
    }
}
