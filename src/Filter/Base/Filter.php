<?php

namespace Papier\Filter\Base;

use Papier\Object\DictionaryObject;

abstract class Filter
{
    /**
     * Decode stream.
     *
     * @param string $stream
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function decode(string $stream, DictionaryObject $param = null): string|bool
    {
        return $stream;
    }

    /**
     * Encode value.
     *
     * @param string $value
     * @param DictionaryObject|null $param
     * @return string|bool
     */
    public static function encode(string $value, DictionaryObject $param = null): string|bool
    {
        return $value;
    }
}