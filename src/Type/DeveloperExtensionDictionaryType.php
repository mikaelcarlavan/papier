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
     * @param NameObject $version
     * @return DeveloperExtensionDictionaryType
     */
    public function setBaseVersion(NameObject $version)
    {
        $this->setEntry('BaseVersion', $version);
        return $this;
    } 

    /**
     * Set extension level.
     *  
     * @param  int $level
     * @return DeveloperExtensionDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setExtensionLevel(int $level)
    {
        if (!IntegerValidator::isValid($level)) {
            throw new InvalidArgumentException("ExtensionLevel is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $level);

        $this->setEntry('ExtensionLevel', $value);
        return $this;
    }
}