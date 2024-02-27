<?php


namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;

class FlateEncodeFilter extends Filter
{
    /**
     * Process value.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function process(string $value, DictionaryObject $param = null): string
    {
        return FlateFilter::decode($value, $param);
    }
}