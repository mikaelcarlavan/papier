<?php

namespace Papier\Type;

use Papier\Type\StreamType;

class TextStreamType extends StreamType
{
     /**
     * Get object's content.
     *  
     * @return string
     */
    protected function getContent()
    {
        $content = parent::getcontent();
        return mb_convert_encoding($content, 'UTF-16BE');
    } 
}