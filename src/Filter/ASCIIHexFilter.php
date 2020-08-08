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
     */
    public static function decode($stream, $param = array())
    {
        $stream = trim($stream);
        $marker = substr($stream, -strlen(self::EOD_MARKER));

        if ($marker != self::EOD_MARKER) {
            throw new InvalidArgumentException("Stream is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $stream = substr($stream, 0, -strlen(self::EOD_MARKER));

        return hex2bin($steam);
    }

    /**
     * Encode value.
     *  
     * @param  string  $value
     * @param  array  $param
     * @return string
     */
    public static function encode($value, $param = array())
    {
        // Clean white-spaces
        $value = preg_replace('/\s+/', '', $value);

        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return bin2hex($value) . self::EOD_MARKER;
    }
}