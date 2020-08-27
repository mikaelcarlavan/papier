<?php

namespace Papier\Type;

use Papier\Object\StreamObject;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;
use Papier\Object\NameObject;
use Papier\Object\DictionaryObject;

use Papier\Validator\StringValidator;


use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class EmbeddedFileStreamType extends StreamObject
{
 
    /**
     * Set subtype.
     *  
     * @param  string  $subtype
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\EmbeddedFileStreamType
     */
    public function setSubtype($subtype)
    {
        if (!StringValidator::isValid($subtype)) {
            throw new InvalidArgumentException("Subtype is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $subtype);

        $this->setEntry('Subtype', $value);
        return $this;
    } 

    /**
     * Set parameters.
     *  
     * @param  \Papier\Object\DictionaryObject  $params
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\ImageType
     */
    public function setParams($params)
    {
        if (!$params instanceof DictionaryObject) {
            throw new InvalidArgumentException("Params is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Params', $params);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $type = Factory::create('Name', 'EmbeddedFile');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}