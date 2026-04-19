<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * ASCIIHexDecode filter (ISO 32000-1 §7.4.2).
 *
 * Encodes each byte as two hexadecimal digits; the encoded data ends
 * with `>` (EOD marker).
 */
final class ASCIIHexDecode implements FilterInterface
{
    public function encode(string $data, ?PdfObject $params = null): string
    {
        return strtoupper(bin2hex($data)) . '>';
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        // Remove whitespace and strip trailing `>`
        $data = preg_replace('/\s+/', '', $data) ?? $data;
        $data = rtrim($data, '>');
        if (!ctype_xdigit($data)) {
            // Be lenient: strip non-hex chars
            $data = preg_replace('/[^0-9A-Fa-f]/', '', $data) ?? $data;
        }
        // Pad to even length
        if (strlen($data) % 2 !== 0) {
            $data .= '0';
        }
        return hex2bin($data) ?: '';
    }
}
