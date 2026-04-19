<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * ASCII85Decode filter (ISO 32000-1 §7.4.3).
 *
 * Encodes binary data using 85 printable ASCII characters (base-85),
 * achieving ~80 % expansion efficiency. The encoded data ends with `~>`.
 */
final class ASCII85Decode implements FilterInterface
{
    public function encode(string $data, ?PdfObject $params = null): string
    {
        $result = '';
        $len    = strlen($data);
        $i      = 0;

        while ($i < $len) {
            $group = substr($data, $i, 4);
            $i    += 4;
            $pad   = 4 - strlen($group);
            $group = str_pad($group, 4, "\x00");

            $b = (ord($group[0]) << 24)
               | (ord($group[1]) << 16)
               | (ord($group[2]) << 8)
               |  ord($group[3]);

            // Treat as unsigned
            $b = $b < 0 ? $b + 4294967296 : $b;

            if ($b === 0 && $pad === 0) {
                $result .= 'z';
            } else {
                $chars = '';
                for ($j = 4; $j >= 0; $j--) {
                    $chars  = chr(($b % 85) + 33) . $chars;
                    $b      = intdiv($b, 85);
                }
                $result .= substr($chars, 0, 5 - $pad);
            }
        }

        $result .= '~>';
        // Wrap at 75 characters for readability
        return wordwrap($result, 75, "\n", true);
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        // Remove whitespace
        $data   = preg_replace('/\s+/', '', $data) ?? $data;
        // Strip EOD marker
        $data   = rtrim($data, '>');
        if (str_ends_with($data, '~')) {
            $data = substr($data, 0, -1);
        }

        $result = '';
        $len    = strlen($data);
        $i      = 0;

        while ($i < $len) {
            if ($data[$i] === 'z') {
                $result .= "\x00\x00\x00\x00";
                $i++;
                continue;
            }

            $group = substr($data, $i, 5);
            $i    += 5;
            $pad   = 5 - strlen($group);
            $group = str_pad($group, 5, 'u'); // pad with 'u' (84+33=117='u' max)

            $b = 0;
            for ($j = 0; $j < 5; $j++) {
                $c = ord($group[$j]) - 33;
                if ($c < 0 || $c > 84) {
                    throw new \RuntimeException("ASCII85Decode: invalid character '{$group[$j]}'.");
                }
                $b = $b * 85 + $c;
            }

            // Extract 4 bytes, skip padded bytes
            $bytes = pack('N', $b);
            $result .= substr($bytes, 0, 4 - $pad);
        }

        return $result;
    }
}
