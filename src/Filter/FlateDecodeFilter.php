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
        // Decode params always set a "decode" filter, so we actually need to "encode" stream
        return FlateFilter::encode($stream, $param);
    }
}