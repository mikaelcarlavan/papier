<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * JPXDecode filter (ISO 32000-1 §7.4.9).
 *
 * JPEG 2000 compression.  Data passes through as-is; rendering is
 * delegated to the PDF viewer.
 */
final class JPXDecode implements FilterInterface
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
