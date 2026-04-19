<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * RunLengthDecode filter (ISO 32000-1 §7.4.5).
 *
 * Simple run-length encoding: a sequence of run-length bytes followed by data.
 *   - 0–127:   literal run; copy next (length+1) bytes.
 *   - 129–255: replicate run; repeat next byte (257−length) times.
 *   - 128:     EOD marker.
 */
final class RunLengthDecode implements FilterInterface
{
    public function encode(string $data, ?PdfObject $params = null): string
    {
        $result = '';
        $len    = strlen($data);
        $i      = 0;

        while ($i < $len) {
            // Look ahead for a run of identical bytes (up to 128)
            $runChar  = $data[$i];
            $runLen   = 1;
            while ($runLen < 128 && ($i + $runLen) < $len && $data[$i + $runLen] === $runChar) {
                $runLen++;
            }

            if ($runLen > 1) {
                // Replicate run
                $result .= chr(257 - $runLen) . $runChar;
                $i      += $runLen;
            } else {
                // Literal run – collect up to 128 non-repeating bytes
                $litStart = $i;
                $litLen   = 0;
                while ($litLen < 128 && ($i + $litLen) < $len) {
                    // Stop if a run of ≥2 identical bytes starts
                    if ($litLen > 0
                        && ($i + $litLen + 1) < $len
                        && $data[$i + $litLen] === $data[$i + $litLen + 1]
                    ) {
                        break;
                    }
                    $litLen++;
                }
                $result .= chr($litLen - 1) . substr($data, $i, $litLen);
                $i      += $litLen;
            }
        }

        $result .= chr(128); // EOD
        return $result;
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        $result = '';
        $len    = strlen($data);
        $i      = 0;

        while ($i < $len) {
            $length = ord($data[$i++]);

            if ($length === 128) {
                break; // EOD
            } elseif ($length < 128) {
                // Literal run
                $count   = $length + 1;
                $result .= substr($data, $i, $count);
                $i      += $count;
            } else {
                // Replicate run
                $count   = 257 - $length;
                $result .= str_repeat($data[$i], $count);
                $i++;
            }
        }

        return $result;
    }
}
