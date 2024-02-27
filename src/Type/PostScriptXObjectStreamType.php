<?php

namespace Papier\Type;

use Papier\Object\StreamObject;
use Papier\Factory\Factory;

class PostScriptXObjectStreamType extends StreamType
{
    /**
     * Set replacing stream when target interpreter supports only LanguageLevel 1.
     *  
     * @param StreamObject $level1
     * @return PostScriptXObjectStreamType
     */
    public function setLevel1(StreamObject $level1): PostScriptXObjectStreamType
    {
        $this->setEntry('Level1', $level1);
        return $this;
    } 

    /**
     * Format object's content.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\NameType', 'PS');
        $this->setEntry('Subtype', $type);
        
        return parent::format();
    }
}