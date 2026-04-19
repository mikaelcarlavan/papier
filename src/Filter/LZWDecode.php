<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * LZWDecode filter (ISO 32000-1 §7.4.4.2).
 *
 * Uses the LZW compression algorithm as specified in TIFF revision 6.0.
 * Supports the EarlyChange parameter (default 1).
 */
final class LZWDecode implements FilterInterface
{
    private const CLEAR_CODE = 256;
    private const EOD_CODE   = 257;

    public function encode(string $data, ?PdfObject $params = null): string
    {
        $earlyChange = 1; // default per spec
        if ($params instanceof \Papier\Objects\PdfDictionary) {
            $ec = $params->get('EarlyChange');
            if ($ec instanceof \Papier\Objects\PdfInteger) {
                $earlyChange = $ec->getValue();
            }
        }

        $table = [];
        for ($i = 0; $i <= 255; $i++) {
            $table[chr($i)] = $i;
        }

        $nextCode   = self::EOD_CODE + 1;
        $codeLength = 9;
        $buffer     = 0;
        $bufLen     = 0;
        $result     = '';

        $addBits = function (int $code) use (&$buffer, &$bufLen, &$result, $codeLength): void {
            $buffer  = ($buffer << $codeLength) | $code;
            $bufLen += $codeLength;
            while ($bufLen >= 8) {
                $bufLen  -= 8;
                $result  .= chr(($buffer >> $bufLen) & 0xFF);
            }
        };

        // Emit CLEAR_CODE
        $addBits(self::CLEAR_CODE);

        $len    = strlen($data);
        $w      = '';
        for ($i = 0; $i < $len; $i++) {
            $c = $data[$i];
            $wc = $w . $c;
            if (isset($table[$wc])) {
                $w = $wc;
            } else {
                $addBits($table[$w]);
                $table[$wc] = $nextCode++;

                // Adjust code length
                $threshold = (1 << $codeLength) - $earlyChange;
                if ($nextCode > $threshold && $codeLength < 12) {
                    $codeLength++;
                }
                if ($nextCode > 4096) {
                    // Reset
                    $addBits(self::CLEAR_CODE);
                    $table = [];
                    for ($j = 0; $j <= 255; $j++) {
                        $table[chr($j)] = $j;
                    }
                    $nextCode   = self::EOD_CODE + 1;
                    $codeLength = 9;
                }
                $w = $c;
            }
        }

        if ($w !== '') {
            $addBits($table[$w]);
        }
        $addBits(self::EOD_CODE);

        // Flush remaining bits
        if ($bufLen > 0) {
            $result .= chr(($buffer << (8 - $bufLen)) & 0xFF);
        }

        return $result;
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        $earlyChange = 1;
        if ($params instanceof \Papier\Objects\PdfDictionary) {
            $ec = $params->get('EarlyChange');
            if ($ec instanceof \Papier\Objects\PdfInteger) {
                $earlyChange = $ec->getValue();
            }
        }

        $table = [];
        for ($i = 0; $i <= 255; $i++) {
            $table[$i] = chr($i);
        }
        $nextCode   = self::EOD_CODE + 1;
        $codeLength = 9;
        $result     = '';
        $buffer     = 0;
        $bufLen     = 0;
        $dataLen    = strlen($data);
        $bytePos    = 0;
        $prev       = null;

        $readCode = function () use (&$buffer, &$bufLen, &$bytePos, $data, $dataLen, &$codeLength): ?int {
            while ($bufLen < $codeLength && $bytePos < $dataLen) {
                $buffer  = ($buffer << 8) | ord($data[$bytePos++]);
                $bufLen += 8;
            }
            if ($bufLen < $codeLength) {
                return null;
            }
            $bufLen -= $codeLength;
            return ($buffer >> $bufLen) & ((1 << $codeLength) - 1);
        };

        while (true) {
            $code = $readCode();
            if ($code === null || $code === self::EOD_CODE) {
                break;
            }
            if ($code === self::CLEAR_CODE) {
                $table = [];
                for ($i = 0; $i <= 255; $i++) {
                    $table[$i] = chr($i);
                }
                $nextCode   = self::EOD_CODE + 1;
                $codeLength = 9;
                $prev       = null;
                continue;
            }

            if (isset($table[$code])) {
                $entry = $table[$code];
            } elseif ($code === $nextCode && $prev !== null) {
                $entry = $prev . $prev[0];
            } else {
                throw new \RuntimeException("LZWDecode: invalid code $code.");
            }

            $result .= $entry;

            if ($prev !== null) {
                $table[$nextCode++] = $prev . $entry[0];
                $threshold = (1 << $codeLength) - $earlyChange;
                if ($nextCode > $threshold && $codeLength < 12) {
                    $codeLength++;
                }
            }
            $prev = $entry;
        }

        return $result;
    }
}
