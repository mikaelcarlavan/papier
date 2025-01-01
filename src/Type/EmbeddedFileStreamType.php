<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\StreamType;

class EmbeddedFileStreamType extends StreamType
{
 
    /**
     * Set subtype.
     *  
     * @param  string  $subtype
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return EmbeddedFileStreamType
     */
    public function setSubtype(string $subtype): EmbeddedFileStreamType
    {
        $value = Factory::create('Papier\Type\Base\NameType', $subtype);

        $this->setEntry('Subtype', $value);
        return $this;
    } 

    /**
     * Set parameters.
     *  
     * @param  DictionaryType  $params
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return EmbeddedFileStreamType
     */
    public function setParams(DictionaryType $params): EmbeddedFileStreamType
    {
        $this->setEntry('Params', $params);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\Base\NameType', 'EmbeddedFile');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}