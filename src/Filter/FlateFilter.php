<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;

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
    public static function encode(string $value, $param = array()): string
    {
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
    public static function decode(string $stream, $param = array()): string
    {
        return gzuncompress($stream);
    }
}