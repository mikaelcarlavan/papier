<?php

namespace Papier\Type;

use Papier\Type\StringType;
use Papier\Object\StreamObject;

class TextStreamType extends StreamObject
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