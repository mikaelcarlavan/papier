<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;
use RuntimeException;

class FlateFilter extends Filter
{
    /**
     * Encode value.
     *
     * @param  string  $value
     * @param  array  $param
     * @return string
     * @throws InvalidArgumentException if the provided argument is not a string.
     */
    public static function encode(string $value, array $param = array()): string
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
     * @param array $param
     * @return string
     * @throws InvalidArgumentException if stream does not end with the end-of-data marker.
     * @throws RuntimeException if stream is empty.
     */
    public static function decode(string $stream, array $param = array()): string
    {
        if (!function_exists('gzuncompress')) {
            throw new RuntimeException("ZLib extension is required for FlateFilter. See ".__CLASS__." class's documentation for possible values.");
        }
        return gzuncompress($stream);
    }
}