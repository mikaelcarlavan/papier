<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * Crypt filter (ISO 32000-1 §7.4.10).
 *
 * Used with encryption; the actual encryption/decryption is performed by
 * the security handler.  This filter is a placeholder that passes data
 * through; real work happens in the Encryption subsystem.
 */
final class Crypt implements FilterInterface
{
    public function encode(string $data, ?PdfObject $params = null): string
    {
        return $data;
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        return $data;
    }
}
