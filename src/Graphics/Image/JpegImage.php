<?php

declare(strict_types=1);

namespace Papier\Graphics\Image;

use Papier\Objects\{PdfDictionary, PdfName, PdfStream};

/**
 * JPEG image XObject (ISO 32000-1 §8.9.5, §7.4.8 DCTDecode filter).
 *
 * JPEG data is stored directly in the stream with the DCTDecode filter.
 * The image dimensions and colour space are extracted from the JPEG header.
 */
final class JpegImage extends PdfImage
{
    public function __construct(private readonly string $jpegData)
    {
        parent::__construct();
        $this->parseJpegHeader();
        $this->stream->setData($this->jpegData);
        $this->stream->getDictionary()->set('Filter', new PdfName('DCTDecode'));
    }

    public static function fromFile(string $path): self
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \InvalidArgumentException("Cannot read image file: $path");
        }
        return new self($data);
    }

    private function parseJpegHeader(): void
    {
        $data = $this->jpegData;
        $len  = strlen($data);
        $pos  = 0;

        // Check SOI marker
        if (substr($data, 0, 2) !== "\xFF\xD8") {
            throw new \InvalidArgumentException('Not a valid JPEG file.');
        }
        $pos = 2;

        while ($pos < $len - 1) {
            if ($data[$pos] !== "\xFF") {
                $pos++;
                continue;
            }
            $marker = ord($data[$pos + 1]);
            $pos   += 2;

            // SOF markers: 0xC0–0xC3, 0xC5–0xC7, 0xC9–0xCB, 0xCD–0xCF
            if (($marker >= 0xC0 && $marker <= 0xC3)
                || ($marker >= 0xC5 && $marker <= 0xC7)
                || ($marker >= 0xC9 && $marker <= 0xCB)
                || ($marker >= 0xCD && $marker <= 0xCF)
            ) {
                $precision  = ord($data[$pos + 2]);
                $this->height = (ord($data[$pos + 3]) << 8) | ord($data[$pos + 4]);
                $this->width  = (ord($data[$pos + 5]) << 8) | ord($data[$pos + 6]);
                $components   = ord($data[$pos + 7]);
                $this->bitsPerComponent = $precision;

                $this->colorSpace = match ($components) {
                    1 => 'DeviceGray',
                    3 => 'DeviceRGB',
                    4 => 'DeviceCMYK',
                    default => 'DeviceRGB',
                };
                return;
            }

            // Skip over marker payload (length includes its own 2 bytes)
            if ($pos + 1 < $len) {
                $segLen  = (ord($data[$pos]) << 8) | ord($data[$pos + 1]);
                $pos    += $segLen;
            }
        }

        throw new \RuntimeException('Could not find JPEG SOF marker.');
    }
}
