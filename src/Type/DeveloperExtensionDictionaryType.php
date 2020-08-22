<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Validator\IntegerValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class DeveloperExtensionDictionaryType extends DictionaryObject
{
    /**
     * Set base version.
     *  
     * @param  \Papier\Object\NameObject  $version
     * @throws InvalidArgumentException if the provided argument is not of type 'NameObject'.
     * @return \Papier\Type\DeveloperExtensionDictionaryType
     */
    public function setBaseVersion($version)
    {
        if (!$version instanceof NameObject) {
            throw new InvalidArgumentException("BaseVersion is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('BaseVersion', $version);
        return $this;
    } 

    /**
     * Set extension level.
     *  
     * @param  int  $renditions
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\DeveloperExtensionDictionaryType
     */
    public function setExtensionLevel($level)
    {
        if (!IntegerValidator::isValid($level)) {
            throw new InvalidArgumentException("ExtensionLevel is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $level);

        $this->setEntry('ExtensionLevel', $value);
        return $this;
    }
}