<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use InvalidArgumentException;

class ExtensionsDictionaryType extends DictionaryObject
{
    /**
     * Add extension.
     * 
     * @param string $name
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return DeveloperExtensionDictionaryType
     */
    public function addExtension(string $name): DeveloperExtensionDictionaryType
    {
        $extension = Factory::create('DeveloperExtensionDictionary');

        $this->setEntry($name, $extension);
        return $extension;
    } 
}