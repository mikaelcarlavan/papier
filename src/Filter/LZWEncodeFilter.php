<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;

class LZWEncodeFilter extends Filter
{
    /**
     * Encode value.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function process(string $value, DictionaryObject $param = null): string|bool
    {
        return LZWFilter::decode($value, $param);
    }
}