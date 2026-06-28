<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * Verbatim PDF object — emits a preformatted byte string unchanged.
 *
 * Used where exact byte layout matters and must not be reflowed by the normal
 * object serialisers, e.g. a signature dictionary that reserves fixed-width
 * placeholders for /ByteRange and /Contents (§12.8).
 */
final class PdfRaw extends PdfObject
{
    public function __construct(private readonly string $raw) {}

    public function toString(): string
    {
        return $this->raw;
    }
}
