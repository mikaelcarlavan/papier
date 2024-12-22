<?php

namespace Papier\Text;

class Encoding
{
    /**
     * Mac Roman encoding
     *
     * @var string
     */
    const MAC_ROMAN = 'MacRomanEncoding';
  
    /**
     * Mac Expert encoding
     *
     * @var string
     */
    const MAC_EXPERT = 'MacExpertEncoding';

    /**
     * Windows ANSI encoding
     *
     * @var string
     */
    const WIN_ANSI = 'WinAnsiEncoding';

    /**
     * Convert string to UTF8 encoding.
     *
     * @param string $value
     * @return string
     */
    public static function toUTF8(string $value): string
    {
        return utf8_encode($value);
    }

    /**
     * Convert string to UTF16-BE encoding.
     *
     * @param string $value
     * @return string
     */
    public static function toUTF16BE(string $value): string
    {
        $out = "\xFE\xFF";
        $i = 1;
		/** @var array<int>|false $characters */
        $characters = unpack("C*", $value);
		if ($characters) {
			while ($i <= count($characters)) {
				$firstByteCharacter = $characters[$i++];

				if ($firstByteCharacter >= 192) { // 192 is 0xC0 = 1100 0000
					$secondByteCharacter = $characters[$i++];

					if ($firstByteCharacter >= 224) { // 224 is 0xE0 = 1110 0000
						$thirdByteCharacter = $characters[$i++];
						$out .= chr((($firstByteCharacter & 0x0F) << 4) + (($secondByteCharacter & 0x3C) >> 2));
						$out .= chr((($secondByteCharacter & 0x03) << 6) + ($thirdByteCharacter & 0x3F));
					} else {
						$out .= chr(($firstByteCharacter & 0x1C) >> 2);
						$out .= chr((($firstByteCharacter & 0x03) << 6) + ($secondByteCharacter & 0x3F));
					}
				} else {
					$out .= "\0".chr($firstByteCharacter);
				}
			}
		}


        return $out;
    }
}