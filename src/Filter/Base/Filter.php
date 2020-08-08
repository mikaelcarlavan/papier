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
    public static function decode($stream, $param = array())
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
    public static function encode($value, $param = array())
    {
        return $value;
    }
}