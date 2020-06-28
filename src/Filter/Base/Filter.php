<?php

namespace Papier\Filter\Base;

abstract class Filter
{
    /**
     * The stream to decode.
     *
     * @var mixed
     */
    protected $stream;

    /**
     * Get object's stream.
     *
     * @return string
     */
    protected function getStream()
    {
        return $this->stream;
    }

    /**
     * Set object's stream.
     *  
     * @param  mixed  $stream
     * @return \Papier\Filter\Base\Filter
     */
    protected function setStream($stream)
    {
        $this->stream = $stream;
        return $this;
    } 

    /**
     * Decode stream.
     *
     * @return string
     */
    public static function decode()
    {
        return $this->stream;
    }
}