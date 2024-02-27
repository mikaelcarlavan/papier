<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use Papier\Factory\Factory;

class DeveloperExtensionDictionaryType extends DictionaryObject
{
    /**
     * Set base version.
     *  
     * @param NameObject $version
     * @return DeveloperExtensionDictionaryType
     */
    public function setBaseVersion(NameObject $version): DeveloperExtensionDictionaryType
    {
        $this->setEntry('BaseVersion', $version);
        return $this;
    } 

    /**
     * Set extension level.
     *  
     * @param  int $level
     * @return DeveloperExtensionDictionaryType
     */
    public function setExtensionLevel(int $level): DeveloperExtensionDictionaryType
    {
        $value = Factory::create('Papier\Type\IntegerType', $level);

        $this->setEntry('ExtensionLevel', $value);
        return $this;
    }
}