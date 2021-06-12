<?php

namespace Papier\Filter\Base;

abstract class Filter
{
    /**
     * Decode stream.
     *
     * @param  string  $stream
     * @param  array  $param
     * @return string
     */
    public static function decode(string $stream, array $param = array()): string
    {
        return $stream;
    }

    /**
     * Encode value.
     *
     * @param  string  $value
     * @param  array  $param
     * @return string
     */
    public static function encode(string $value, array $param = array()): string
    {
        return $value;
    }
}