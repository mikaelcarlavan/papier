<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfDictionary;
use Papier\Objects\PdfInteger;
use Papier\Objects\PdfObject;

/**
 * FlateDecode filter (ISO 32000-1 §7.4.4).
 *
 * Uses the zlib/deflate compression format (RFC 1950).
 * Supports the optional Predictor parameter for additional PNG/TIFF
 * pre-/post-processing.
 */
final class FlateDecode implements FilterInterface
{
    public function encode(string $data, ?PdfObject $params = null): string
    {
        $predictor = $this->getPredictor($params);
        if ($predictor >= 10) {
            $data = $this->applyPngPredictor($data, $params);
        } elseif ($predictor === 2) {
            $data = $this->applyTiffPredictor($data, $params);
        }

        $compressed = gzcompress($data, 6);
        if ($compressed === false) {
            throw new \RuntimeException('FlateDecode: compression failed.');
        }
        return $compressed;
    }

    public function decode(string $data, ?PdfObject $params = null): string
    {
        $decompressed = @gzuncompress($data);
        if ($decompressed === false) {
            // Try inflate (without zlib header)
            $decompressed = @gzinflate($data);
            if ($decompressed === false) {
                throw new \RuntimeException('FlateDecode: decompression failed.');
            }
        }

        $predictor = $this->getPredictor($params);
        if ($predictor >= 10) {
            return $this->reversePngPredictor($decompressed, $params);
        } elseif ($predictor === 2) {
            return $this->reverseTiffPredictor($decompressed, $params);
        }
        return $decompressed;
    }

    // ── Predictor helpers ─────────────────────────────────────────────────────

    private function getPredictor(?PdfObject $params): int
    {
        if ($params instanceof PdfDictionary) {
            $p = $params->get('Predictor');
            if ($p instanceof PdfInteger) {
                return $p->getValue();
            }
        }
        return 1; // no predictor
    }

    private function getColumns(?PdfObject $params): int
    {
        if ($params instanceof PdfDictionary) {
            $c = $params->get('Columns');
            if ($c instanceof PdfInteger) {
                return $c->getValue();
            }
        }
        return 1;
    }

    private function getColors(?PdfObject $params): int
    {
        if ($params instanceof PdfDictionary) {
            $c = $params->get('Colors');
            if ($c instanceof PdfInteger) {
                return $c->getValue();
            }
        }
        return 1;
    }

    private function getBitsPerComponent(?PdfObject $params): int
    {
        if ($params instanceof PdfDictionary) {
            $b = $params->get('BitsPerComponent');
            if ($b instanceof PdfInteger) {
                return $b->getValue();
            }
        }
        return 8;
    }

    /**
     * Apply PNG prediction (predictor 10–15) before compression.
     * Predictor 15 = PNG Optimum (choose per row).
     */
    private function applyPngPredictor(string $data, ?PdfObject $params): string
    {
        $columns = $this->getColumns($params);
        $colors  = $this->getColors($params);
        $bpc     = $this->getBitsPerComponent($params);
        $bpp     = (int) ceil($colors * $bpc / 8); // bytes per pixel
        $rowLen  = (int) ceil($columns * $colors * $bpc / 8);

        $result = '';
        $offset = 0;
        $len    = strlen($data);
        while ($offset < $len) {
            $row  = substr($data, $offset, $rowLen);
            $offset += $rowLen;
            // Sub filter (predictor 11) – good for most images
            $filtered = "\x01"; // filter byte
            $prev     = str_repeat("\x00", $bpp);
            for ($i = 0; $i < $rowLen; $i++) {
                $a          = $i >= $bpp ? ord($row[$i - $bpp]) : 0;
                $filtered  .= chr((ord($row[$i]) - $a) & 0xFF);
            }
            $result .= $filtered;
        }
        return $result;
    }

    private function applyTiffPredictor(string $data, ?PdfObject $params): string
    {
        $columns = $this->getColumns($params);
        $colors  = $this->getColors($params);
        $bpc     = $this->getBitsPerComponent($params);
        $bpp     = (int) ceil($colors * $bpc / 8);
        $rowLen  = (int) ceil($columns * $colors * $bpc / 8);

        $result = '';
        $offset = 0;
        $len    = strlen($data);
        while ($offset < $len) {
            $row     = substr($data, $offset, $rowLen);
            $offset += $rowLen;
            $out     = $row;
            for ($i = $rowLen - 1; $i >= $bpp; $i--) {
                $out[$i] = chr((ord($row[$i]) - ord($row[$i - $bpp])) & 0xFF);
            }
            $result .= $out;
        }
        return $result;
    }

    private function reversePngPredictor(string $data, ?PdfObject $params): string
    {
        $columns = $this->getColumns($params);
        $colors  = $this->getColors($params);
        $bpc     = $this->getBitsPerComponent($params);
        $bpp     = (int) ceil($colors * $bpc / 8);
        $rowLen  = (int) ceil($columns * $colors * $bpc / 8);

        $result = '';
        $offset = 0;
        $len    = strlen($data);
        while ($offset < $len) {
            $filter  = ord($data[$offset++]);
            $row     = substr($data, $offset, $rowLen);
            $offset += $rowLen;
            $prev    = isset($prevRow) ? $prevRow : str_repeat("\x00", $rowLen);
            $out     = str_repeat("\x00", $rowLen);

            for ($i = 0; $i < $rowLen; $i++) {
                $x   = ord($row[$i]);
                $a   = $i >= $bpp ? ord($out[$i - $bpp]) : 0;
                $b   = ord($prev[$i]);
                $c   = $i >= $bpp ? ord($prev[$i - $bpp]) : 0;
                $out[$i] = chr(match ($filter) {
                    0 => $x,                    // None
                    1 => ($x + $a) & 0xFF,      // Sub
                    2 => ($x + $b) & 0xFF,      // Up
                    3 => ($x + (int)(($a + $b) / 2)) & 0xFF, // Average
                    4 => ($x + $this->paethPredictor($a, $b, $c)) & 0xFF, // Paeth
                    default => $x,
                });
            }
            $prevRow  = $out;
            $result  .= $out;
        }
        return $result;
    }

    private function reverseTiffPredictor(string $data, ?PdfObject $params): string
    {
        $columns = $this->getColumns($params);
        $colors  = $this->getColors($params);
        $bpc     = $this->getBitsPerComponent($params);
        $bpp     = (int) ceil($colors * $bpc / 8);
        $rowLen  = (int) ceil($columns * $colors * $bpc / 8);

        $result = '';
        $offset = 0;
        $len    = strlen($data);
        while ($offset < $len) {
            $row     = substr($data, $offset, $rowLen);
            $offset += $rowLen;
            $out     = $row;
            for ($i = $bpp; $i < $rowLen; $i++) {
                $out[$i] = chr((ord($out[$i]) + ord($out[$i - $bpp])) & 0xFF);
            }
            $result .= $out;
        }
        return $result;
    }

    private function paethPredictor(int $a, int $b, int $c): int
    {
        $p  = $a + $b - $c;
        $pa = abs($p - $a);
        $pb = abs($p - $b);
        $pc = abs($p - $c);
        if ($pa <= $pb && $pa <= $pc) {
            return $a;
        } elseif ($pb <= $pc) {
            return $b;
        }
        return $c;
    }
}
