<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Null object (ISO 32000-1 §7.3.9).
 *
 * Has a type and value that are unequal to those of any other object.
 */
final class PdfNull extends PdfObject
{
    public function toString(): string
    {
        return 'null';
    }
}
