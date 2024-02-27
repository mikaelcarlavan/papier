<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;
use RuntimeException;

class FlateFilter extends Filter
{
    /**
     * Encode value.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function encode(string $value, DictionaryObject $param = null): string
    {
        if (!function_exists('gzcompress')) {
            throw new RuntimeException("ZLib extension is required for FlateFilter. See ".__CLASS__." class's documentation for possible values.");
        }
        return gzcompress($value);
    }

    /**
     * Decode stream.
     *
     * @param string $stream
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function decode(string $stream, DictionaryObject $param = null): string
    {
        if (!function_exists('gzuncompress')) {
            throw new RuntimeException("ZLib extension is required for FlateFilter. See ".__CLASS__." class's documentation for possible values.");
        }
        return gzuncompress($stream);
    }
}