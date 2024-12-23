<?php

namespace Papier\Helpers;

use Papier\Filter\FilterType;
use Papier\Filter\FlateDecodeFilter;
use Papier\Filter\FlateFilter;
use Papier\Component\ImageComponent;
use InvalidArgumentException;

class ImageHelper
{
    /**
     * Read file and returns image colors and transparency.
     *
     * @param string $file
     * @return array<mixed>
	 */
    public static function getDataFromSource(string $file): array
	{
        $dimensions = getimagesize($file);

        $mime = $dimensions['mime'] ?? null;

		$data = null;
		$mask = null;
        if ($mime == 'image/jpeg') {
            $data = ImageHelper::getDataFromJPEG($file);
        } else if ($mime == 'image/png') {
			list($data, $mask) = ImageHelper::getDataFromPNG($file);
        }

        return array($data, $mask);
    }

    /**
     * Read JPG/JPEG file and returns compressed data.
     *
     * @param string $file
     * @return string|bool
     */
    public static function getDataFromJPEG(string $file): string|bool
    {
		return file_get_contents($file);
    }

    /**
     * Read PNG file and returns compressed colors and transparency.
     *
     * @param string $file
     * @return array<mixed>
	 */
    public static function getDataFromPNG(string $file): array
	{
        $data = null;

        // Open
        $stream = FileHelper::getInstance()->open($file);
		$stream->setBigEndian();

        $header = $stream->read(8);

        // PNG signature, check https://en.wikipedia.org/wiki/PNG
        if ($header != "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a") {
            throw new InvalidArgumentException("Source is not a valid PNG file. See ".__CLASS__." class's documentation for possible values.");
        }

        // Read first chunk (should be IHDR type)
        $length = $stream->unpackUnsignedInteger(); // Should be equal to 13
        $type = $stream->read(4);
        if ($type != "IHDR") {
            throw new InvalidArgumentException("Source is not a valid PNG file. See ".__CLASS__." class's documentation for possible values.");
        }

		$width = $stream->unpackUnsignedInteger();
        $height = $stream->unpackUnsignedInteger();
        $bitDepth = $stream->unpackByte();
        $colorType = $stream->unpackByte();

        $compressionMethod = $stream->unpackByte();
        $filterMethod = $stream->unpackByte();
        $interlaceMethod = $stream->unpackByte();

        $crc = $stream->read(4);
		$pixels = $width * $height;

        while ($type != 'IEND') {
            $length = $stream->unpackUnsignedInteger();
            $type = $stream->read(4);

            if ($type == 'IDAT') { // Data
                $data .= $stream->read($length);
            } else if ($length > 0) {
                $stream->read($length);
            }

            $crc = $stream->read(4);
        }

        $stream->close();

		$mask = null;

		// Image has a transparency channel
		if ($colorType >= 4) {
			$colors = null;
			$data = FlateFilter::decode($data);

			if ($data !== false) {
				$channels = $colorType == 4 ? 2 : 4; // Gray + alpha or RBG + alpha
				$data = str_split((string)$data);

				$pixel = 0;
				for ($row = 0; $row < $height; $row++) {
					$colors .= $data[$pixel]; // Filter type
					$mask .= $data[$pixel]; // Filter type
					$pixel++;
					// Data
					for ($column = 0; $column < $width; $column++) {
						for ($color = 0; $color < $channels - 1; $color++) {
							$colors .= $data[$pixel];
							$pixel++;
						}

						$mask .= $data[$pixel];
						$pixel++;
					}
				}

				$data = FlateFilter::encode($colors);
				$mask = FlateFilter::encode($mask);
			}
		}

        return array($data, $mask);
    }
}