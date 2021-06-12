<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;

class LZWEncodeFilter extends Filter
{
    /**
     * Encode value.
     *
     * @param  string  $value
     * @param  array  $param
     * @return string
     * @throws InvalidArgumentException if the provided argument is not a string.
     */
    public static function process(string $value, array $param = array()): string
    {
        return LZWFilter::decode($value, $param);
    }
}