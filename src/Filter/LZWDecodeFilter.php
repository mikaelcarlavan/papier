<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;

class LZWDecodeFilter extends Filter
{
    /**
     * Encode stream.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function process(string $value, DictionaryObject $param = null): string|bool
    {
        return LZWFilter::encode($value, $param);
    }
}