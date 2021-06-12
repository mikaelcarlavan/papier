<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;

class LZWDecodeFilter extends Filter
{
    /**
     * Encode stream.
     *
     * @param  string  $value
     * @param  array  $param
     * @return string
     * @throws InvalidArgumentException if stream does not end with the end-of-data marker.
     * @throws RuntimeException if stream is empty.
     */
    public static function process(string $value, array $param = array()): string
    {
        return LZWFilter::encode($value, $param);
    }
}