<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;

class ASCIIHexFilter extends Filter
{    
    /**
     * Decode stream.
     *  
     * @param  string  $stream
     * @return string
     */
    public function decode($stream)
    {
        return $stream;
    }
}