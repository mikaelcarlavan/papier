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
     * @return string
     */
    public static function process(string $value, DictionaryObject $param = null): string
    {
        return LZWFilter::decode($value, $param);
    }
}