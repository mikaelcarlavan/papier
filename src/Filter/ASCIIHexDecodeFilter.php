<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Object\DictionaryObject;

class ASCIIHexDecodeFilter extends Filter
{
    /**
     * Process stream.
     *
     * @param string $stream
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function process(string $stream, DictionaryObject $param = null): string|bool
    {
        return ASCIIHexFilter::encode($stream, $param);
    }
}