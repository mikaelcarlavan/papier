<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;

class ASCIIHexEncodeFilter extends Filter
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
        return ASCIIHexFilter::decode($value, $param);
    }
}