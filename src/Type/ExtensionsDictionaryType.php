<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use InvalidArgumentException;
use Papier\Type\Base\DictionaryType;

class ExtensionsDictionaryType extends DictionaryType
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
		/** @var DeveloperExtensionDictionaryType $extension */
        $extension = Factory::create('Papier\Type\DeveloperExtensionDictionaryType');

        $this->setEntry($name, $extension);
        return $extension;
    } 
}