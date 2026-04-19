<?php

declare(strict_types=1);

namespace Papier\Graphics\Image;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfStream, PdfString};

/**
 * PNG image XObject (ISO 32000-1 §8.9, §7.4.4 FlateDecode).
 *
 * Reads PNG files and converts them to PDF image streams.
 * Supports grayscale, RGB, indexed, and RGBA (with alpha as SMask).
 *
 * For non-alpha images the IDAT bytes are embedded as-is: the PNG zlib stream
 * (filtered rows) is exactly what PDF FlateDecode + Predictor=15 expects.
 * For RGBA/grayscale+alpha the channels are split: colour goes to the image
 * stream, alpha goes to a companion SMask stream, both stored as raw pixels
 * (FlateDecode, no predictor).
 */
final class PngImage extends PdfImage
{
    private ?PdfStream $sMaskStream = null;
    private bool       $hasAlpha    = false;

    public function __construct(private readonly string $pngData)
    {
        parent::__construct();
        $this->parsePng();
    }

    public static function fromFile(string $path): self
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \InvalidArgumentException("Cannot read PNG file: $path");
        }
        return new self($data);
    }

    public function getSMaskStream(): ?PdfStream { return $this->sMaskStream; }

    public function hasAlpha(): bool { return $this->hasAlpha; }

    private function parsePng(): void
    {
        $data = $this->pngData;
        if (substr($data, 0, 8) !== "\x89PNG\r\n\x1A\n") {
            throw new \InvalidArgumentException('Not a valid PNG file.');
        }

        $pos       = 8;
        $len       = strlen($data);
        $idat      = '';
        $colorType = 0;
        $bitDepth  = 8;
        $palette   = '';

        while ($pos + 12 <= $len) {
            $chunkLen  = unpack('N', substr($data, $pos, 4))[1];
            $chunkType = substr($data, $pos + 4, 4);
            $chunkData = substr($data, $pos + 8, $chunkLen);
            $pos      += 12 + $chunkLen;

            switch ($chunkType) {
                case 'IHDR':
                    $this->width  = unpack('N', substr($chunkData, 0, 4))[1];
                    $this->height = unpack('N', substr($chunkData, 4, 4))[1];
                    $bitDepth     = ord($chunkData[8]);
                    $colorType    = ord($chunkData[9]);
                    break;
                case 'PLTE':
                    $palette = $chunkData;
                    break;
                case 'IDAT':
                    $idat .= $chunkData;
                    break;
                case 'IEND':
                    break 2;
            }
        }

        $this->bitsPerComponent = $bitDepth;
        $hasAlpha = ($colorType === 4 || $colorType === 6);
        $this->hasAlpha = $hasAlpha;

        // Number of channels in the PNG pixel data
        $channels = match ($colorType) { 0 => 1, 2 => 3, 3 => 1, 4 => 2, 6 => 4, default => 3 };
        // Colour channels only (excluding alpha)
        $colorChans = $hasAlpha ? $channels - 1 : $channels;
        $bytesPerSample = (int) ceil($bitDepth / 8);

        if (!$hasAlpha) {
            // ── Non-alpha path ─────────────────────────────────────────────────
            // The concatenated IDAT bytes are a single zlib stream whose payload
            // is PNG-filtered rows (filter_type_byte + filtered_pixels per row).
            // PDF FlateDecode + Predictor=15 decodes this natively — no manual
            // decompression needed.
            $this->stream->setData($idat);
            $this->stream->getDictionary()->set('Filter', new PdfName('FlateDecode'));
            $this->stream->getDictionary()->set('DecodeParms', $this->buildDecodeParms($colorChans, $bytesPerSample));

            // Colour space
            switch ($colorType) {
                case 0:
                    $this->colorSpace = 'DeviceGray';
                    break;
                case 2:
                    $this->colorSpace = 'DeviceRGB';
                    break;
                case 3: // Indexed — set [/Indexed /DeviceRGB hival <palette>]
                    $paletteCount = (int)(strlen($palette) / 3);
                    $cs = new PdfArray();
                    $cs->add(new PdfName('Indexed'));
                    $cs->add(new PdfName('DeviceRGB'));
                    $cs->add(new PdfInteger($paletteCount - 1));
                    $cs->add(new PdfString($palette));
                    // Inject directly so getStream() won't overwrite with a plain PdfName
                    $this->stream->getDictionary()->set('ColorSpace', $cs);
                    $this->colorSpace = ''; // sentinel: already set above
                    break;
            }
        } else {
            // ── Alpha path ─────────────────────────────────────────────────────
            // Must decompress, reconstruct pixels, then split colour and alpha.
            $rawData = gzuncompress($idat);
            if ($rawData === false) {
                throw new \RuntimeException('PngImage: failed to decompress IDAT.');
            }

            $colorChansAlpha = $colorType === 4 ? 1 : 3;
            $this->colorSpace = $colorType === 4 ? 'DeviceGray' : 'DeviceRGB';

            $stride  = 1 + $this->width * $channels * $bytesPerSample;
            $prevRow = str_repeat("\x00", $this->width * $channels * $bytesPerSample);
            $imgBytes   = '';
            $alphaBytes = '';
            $rawPos = 0;

            for ($y = 0; $y < $this->height; $y++) {
                $filter  = ord($rawData[$rawPos++]);
                $rowData = substr($rawData, $rawPos, $this->width * $channels * $bytesPerSample);
                $rawPos += $this->width * $channels * $bytesPerSample;

                $row     = $this->reconstructRow($filter, $rowData, $prevRow, $channels * $bytesPerSample);
                $prevRow = $row;

                for ($x = 0; $x < $this->width; $x++) {
                    $pxOff = $x * $channels * $bytesPerSample;
                    for ($c = 0; $c < $colorChansAlpha * $bytesPerSample; $c++) {
                        $imgBytes .= $row[$pxOff + $c];
                    }
                    for ($c = 0; $c < $bytesPerSample; $c++) {
                        $alphaBytes .= $row[$pxOff + $colorChansAlpha * $bytesPerSample + $c];
                    }
                }
            }

            // Colour stream — plain raw pixels, FlateDecode, no predictor
            $this->stream->setData(gzcompress($imgBytes, 6) ?: $imgBytes);
            $this->stream->getDictionary()->set('Filter', new PdfName('FlateDecode'));

            // Soft-mask (alpha) stream
            $sMask = new PdfStream();
            $sMask->getDictionary()->set('Type',             new PdfName('XObject'));
            $sMask->getDictionary()->set('Subtype',          new PdfName('Image'));
            $sMask->getDictionary()->set('Width',            new PdfInteger($this->width));
            $sMask->getDictionary()->set('Height',           new PdfInteger($this->height));
            $sMask->getDictionary()->set('ColorSpace',       new PdfName('DeviceGray'));
            $sMask->getDictionary()->set('BitsPerComponent', new PdfInteger($bitDepth));
            $sMask->getDictionary()->set('Filter',           new PdfName('FlateDecode'));
            $sMask->setData(gzcompress($alphaBytes, 6) ?: $alphaBytes);
            $this->sMaskStream = $sMask;
        }
    }

    private function buildDecodeParms(int $colors, int $bytesPerSample): PdfDictionary
    {
        $dp = new PdfDictionary();
        $dp->set('Predictor',       new PdfInteger(15));
        $dp->set('Colors',          new PdfInteger($colors));
        $dp->set('BitsPerComponent', new PdfInteger($this->bitsPerComponent));
        $dp->set('Columns',         new PdfInteger($this->width));
        return $dp;
    }

    private function reconstructRow(int $filter, string $row, string $prev, int $bpp): string
    {
        $len = strlen($row);
        $out = str_repeat("\x00", $len);
        for ($i = 0; $i < $len; $i++) {
            $x = ord($row[$i]);
            $a = $i >= $bpp ? ord($out[$i - $bpp]) : 0;
            $b = isset($prev[$i]) ? ord($prev[$i]) : 0;
            $c = ($i >= $bpp && isset($prev[$i - $bpp])) ? ord($prev[$i - $bpp]) : 0;
            $out[$i] = chr(match ($filter) {
                0 => $x,
                1 => ($x + $a) & 0xFF,
                2 => ($x + $b) & 0xFF,
                3 => ($x + (int)(($a + $b) / 2)) & 0xFF,
                4 => ($x + $this->paeth($a, $b, $c)) & 0xFF,
                default => $x,
            });
        }
        return $out;
    }

    private function paeth(int $a, int $b, int $c): int
    {
        $p  = $a + $b - $c;
        $pa = abs($p - $a);
        $pb = abs($p - $b);
        $pc = abs($p - $c);
        if ($pa <= $pb && $pa <= $pc) { return $a; }
        if ($pb <= $pc) { return $b; }
        return $c;
    }
}
