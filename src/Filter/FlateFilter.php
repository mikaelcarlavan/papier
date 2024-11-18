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
     * @param ?string $value
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function encode(?string $value, DictionaryObject $param = null): string|bool
    {
        if (!function_exists('gzcompress')) {
            throw new RuntimeException("ZLib extension is required for FlateFilter. See ".__CLASS__." class's documentation for possible values.");
        }
        return is_null($value) ? false : gzcompress($value);
    }

    /**
     * Decode stream.
     *
     * @param ?string $stream
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function decode(?string $stream, DictionaryObject $param = null): string|bool
    {
        if (!function_exists('gzuncompress')) {
            throw new RuntimeException("ZLib extension is required for FlateFilter. See ".__CLASS__." class's documentation for possible values.");
        }
        return is_null($stream) ? false : gzuncompress($stream);
    }
}