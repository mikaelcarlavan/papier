<?php

namespace Papier\Type;

class TextStreamType extends StreamType
{
     /**
     * Get object's content.
     *  
     * @return string
     */
    protected function getContent(): string
    {
        $content = parent::getcontent();
        return mb_convert_encoding($content, 'UTF-16BE');
    } 
}