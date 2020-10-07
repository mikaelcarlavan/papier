<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\IndirectObject;


use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class CollectionItemDictionaryType extends DictionaryObject
{
    /**
     * Set object for key.
     *  
     * @param  string  $key
     * @param  mixed  $object
     * @throws InvalidArgumentException if the provided argument is not of type 'IndirectObject'.
     * @return \Papier\Type\CollectionItemDictionaryType
     */
    public function setObject(string $key, $object)
    {
        if (!StringValidator::isValid($key)) {
            throw new InvalidArgumentException("Key is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("FS is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $fs);

        $this->setEntry($key, $$object);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $type = Factory::create('Name', 'CollectionItem');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}