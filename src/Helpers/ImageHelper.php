<?php

namespace Papier\Helpers;

use http\Exception\InvalidArgumentException;
use Papier\Filter\FilterType;
use Papier\Widget\ImageWidget;

class ImageHelper
{
    /**
     * Read file and returns image data.
     *
     * @param string $file
     * @return string|null
     */
    public static function getDataFromSource(string $file): ?string
    {
        $dimensions = getimagesize($file);

        $mime = $dimensions['mime'] ?? null;

        if ($mime == 'image/jpeg') {
            return ImageHelper::getDataFromJPEG($file);
        } else if ($mime == 'image/png') {
            return ImageHelper::getDataFromPNG($file);
        }

        return null;
    }

    /**
     * Read JPG/JPEG file and returns compressed data.
     *
     * @param string $file
     * @return string
     */
    public static function getDataFromJPEG(string $file): string
    {
        return file_get_contents($file);
    }

    /**
     * Read PNG file and returns compressed data.
     *
     * @param string $file
     * @return string|null
     */
    public static function getDataFromPNG(string $file): ?string
    {
        $data = null;

        // Open
        $stream = FileHelper::getInstance()->open($file);

        $header = $stream->read(8);
        // PNG signature, check https://en.wikipedia.org/wiki/PNG
        if ($header != hex2bin("89504e470d0a1a0a")) {
            throw new InvalidArgumentException("Source is not a valid PNG file. See ".__CLASS__." class's documentation for possible values.");
        }

        // Read first chunk (should be IHDR type)
        $length = $stream->unpackInteger(); // Should be equal to 13
        $type = $stream->read(4);
        if ($type != "IHDR") {
            throw new InvalidArgumentException("Source is not a valid PNG file. See ".__CLASS__." class's documentation for possible values.");
        }

        $width = $stream->unpackInteger();
        $height = $stream->unpackInteger();
        $bitDepth = $stream->unpackByte();
        $colorType = $stream->unpackByte();

        $compressionMethod = $stream->unpackByte();
        $filterMethod = $stream->unpackByte();
        $interlaceMethod = $stream->unpackByte();

        $crc = $stream->read(4);

        while ($type != 'IEND') {
            $length = $stream->unpackInteger();
            $type = $stream->read(4);

            if ($type == 'IDAT') { // Data
                $data .= $stream->read($length);
            } else if ($length > 0) {
                $stream->read($length);
            }

            $crc = $stream->read(4);
        }

        $stream->close();

        return $data;
    }
}