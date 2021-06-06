<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Validator\StringValidator;

use RuntimeException;
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
     * @param  string  $stream
     * @param  array  $param
     * @return string
     * @throws InvalidArgumentException if stream does not end with the end-of-data marker.
     * @throws RuntimeException if stream is empty.
     */
    public static function decode(string $stream, $param = array()): string
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
     * @param  string  $value
     * @param  array  $param
     * @return string
     * @throws InvalidArgumentException if the provided argument is not a string.
     */
    public static function encode(string $value, $param = array()): string
    {
        // Clean white-spaces
        $value = preg_replace('/\s+/', '', $value);

        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return bin2hex($value) . self::EOD_MARKER;
    }
}