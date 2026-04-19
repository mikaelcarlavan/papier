<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * CCITTFaxDecode filter (ISO 32000-1 §7.4.6).
 *
 * CCITT (fax) compressed data.  Full CCITT Group 3/4 encoding is beyond the
 * scope of a pure-PHP implementation; the filter is registered and passes data
 * through for viewer rendering.  Callers should only use this filter with
 * bilevel (1 bpc) image data.
 */
final class CCITTFaxDecode implements FilterInterface
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
