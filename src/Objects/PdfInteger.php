<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Integer object (ISO 32000-1 §7.3.3).
 *
 * A signed integer value with no fractional part.
 * Implementation limits: ±2^31−1 (Annex C).
 */
final class PdfInteger extends PdfObject
{
    public function __construct(private readonly int $value) {}

    public function getValue(): int
    {
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }
}
