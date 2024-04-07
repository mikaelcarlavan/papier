<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Object\IndirectObject;
use Papier\Validator\StringValidator;

class CollectionItemDictionaryType extends DictionaryObject
{
    /**
     * Set object for key.
     *  
     * @param  string  $key
     * @param  mixed  $object
     * @throws InvalidArgumentException if the provided argument is not of type 'IndirectObject'.
     * @return CollectionItemDictionaryType
     */
    public function setObject(string $key, $object): CollectionItemDictionaryType
    {
        if (!StringValidator::isValid($key)) {
            throw new InvalidArgumentException("Key is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("FS is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry($key, $object);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\NameType', 'CollectionItem');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}