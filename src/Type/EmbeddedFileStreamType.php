<?php

namespace Papier\Type;

use Papier\Object\StreamObject;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use InvalidArgumentException;

class EmbeddedFileStreamType extends StreamObject
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
        $value = Factory::create('Papier\Type\NameType', $subtype);

        $this->setEntry('Subtype', $value);
        return $this;
    } 

    /**
     * Set parameters.
     *  
     * @param  DictionaryObject  $params
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return EmbeddedFileStreamType
     */
    public function setParams(DictionaryObject $params): EmbeddedFileStreamType
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
        $type = Factory::create('Papier\Type\NameType', 'EmbeddedFile');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}