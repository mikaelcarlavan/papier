<?php

namespace Papier\Type;

use Papier\Type\StreamType;
use Papier\Object\StreamObject;

class PostScriptXObjectStreamType extends StreamType
{
    /**
     * Set replacing stream when target interpreter supports only LanguageLevel 1.
     *  
     * @param  \Papier\Object\StreamObject  $level1
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\PostScriptXObjectStreamType
     */
    public function setLevel1($level1)
    {
        if (!$level1 instanceof StreamObject) {
            throw new InvalidArgumentException("Level1 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Level1', $level1);
        return $this;
    } 

    /**
     * Format object's content.
     *
     * @return string
     */
    public function format()
    {
        $type = Factory::create('Name', 'PS');
        $this->setEntry('Subtype', $type);
        
        return parent::format();
    }
}