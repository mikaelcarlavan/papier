<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * JBIG2Decode filter (ISO 32000-1 §7.4.7).
 *
 * JBIG2 compressed monochrome data. Data passes through; viewer handles it.
 */
final class JBIG2Decode implements FilterInterface
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
