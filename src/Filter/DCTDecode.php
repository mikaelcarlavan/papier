<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * DCTDecode filter (ISO 32000-1 §7.4.8).
 *
 * JPEG (DCT) compression.  In practice, the data is already a JPEG stream
 * (from file), so encode is a no-op (JPEG is encoded by the image source)
 * and decode simply returns the raw bytes for rendering.
 */
final class DCTDecode implements FilterInterface
{
    public function encode(string $data, ?PdfObject $params = null): string
    {
        // Data is already JPEG-encoded; return as-is
        return $data;
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        // Return raw JPEG bytes; the viewer handles rendering
        return $data;
    }
}
