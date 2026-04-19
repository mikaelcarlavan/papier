<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Boolean object (ISO 32000-1 §7.3.2).
 *
 * Represented as the keyword `true` or `false`.
 */
final class PdfBoolean extends PdfObject
{
    public function __construct(private readonly bool $value) {}

    public static function true(): self
    {
        return new self(true);
    }

    public static function false(): self
    {
        return new self(false);
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value ? 'true' : 'false';
    }
}
