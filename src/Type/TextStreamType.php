<?php

namespace Papier\Type;
use Papier\Text\Encoding;
use Papier\Type\Base\StreamType;

class TextStreamType extends StreamType
{
     /**
     * Get object's content.
     *  
     * @return ?string
     */
    protected function getContent(): ?string
    {
        $content = parent::getcontent();
        return is_null($content) ? $content : Encoding::toUTF16BE($content);
    } 
}