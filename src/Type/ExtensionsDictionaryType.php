<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\StringValidator;

use InvalidArgumentException;

class ExtensionsDictionaryType extends DictionaryObject
{
    /**
     * Add extension.
     * 
     * @param string $name
     * @return \Papier\Type\DeveloperExtensionDictionaryType
     */
    public function addExtension($name)
    {
        if (!StringValidator::isValid($name)) {
            throw new InvalidArgumentException("Name is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $extension = Factory::create('DeveloperExtensionDictionary', null, false);

        $this->setEntry($name, $extension);
        return $extension;
    } 
}