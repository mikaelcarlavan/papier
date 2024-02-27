<?php


namespace Papier\Filter;


use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;

class FlateDecodeFilter extends Filter
{
    /**
     * Process stream.
     *
     * @param string $stream
     * @param DictionaryObject|null $param
     * @return string
     */
    public static function process(string $stream, DictionaryObject $param = null): string
    {
        return FlateFilter::encode($stream, $param);
    }
}