<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * Contract for PDF stream filters (ISO 32000-1 §7.4).
 */
interface FilterInterface
{
    /**
     * Encode (compress / transform) raw data.
     *
     * @param string         $data   Raw bytes to encode.
     * @param PdfObject|null $params Filter parameters dictionary (DecodeParms entry).
     */
    public function encode(string $data, ?PdfObject $params = null): string;

    /**
     * Decode (decompress / inverse-transform) filtered data.
     *
     * @param string         $data   Encoded bytes to decode.
     * @param PdfObject|null $params Filter parameters dictionary (DecodeParms entry).
     */
    public function decode(string $data, ?PdfObject $params = null): string;
}
