<?php

namespace Papier\Type;
use Papier\Text\Encoding;

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
        return Encoding::toUTF16BE($content);
    } 
}