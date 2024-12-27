<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

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
        $value = Factory::create('Papier\Type\Base\IntegerType', $level);

        $this->setEntry('ExtensionLevel', $value);
        return $this;
    }
}