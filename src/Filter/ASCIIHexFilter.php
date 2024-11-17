<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class ASCIIHexFilter extends Filter
{  
    /**
     * End-of-data marker.
     *
     * @var string
     */
    const EOD_MARKER = ">";

    /**
     * Decode stream.
     *
     * @param string $stream
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function decode(string $stream, DictionaryObject $param = null): string|bool
    {
        $stream = trim($stream);
        $marker = substr($stream, -strlen(self::EOD_MARKER));

        if ($marker != self::EOD_MARKER) {
            throw new InvalidArgumentException("Stream is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $stream = substr($stream, 0, -strlen(self::EOD_MARKER));

        return hex2bin($stream);
    }

    /**
     * Encode value.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function encode(string $value, DictionaryObject $param = null): string
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return bin2hex($value) . self::EOD_MARKER;
    }
}