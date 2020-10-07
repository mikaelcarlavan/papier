<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class DeviceNColourSpaceAttributesDictionaryType extends DictionaryObject
{
    /**
     * Set subtype.
     *  
     * @param  string  $subtype
     * @return DeviceNColourSpaceAttributesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setSubtype(string $subtype)
    {
        if (!StringValidator::isValid($subtype)) {
            throw new InvalidArgumentException("Subtype is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $subtype);

        $this->setEntry('Subtype', $value);
        return $this;
    } 

    /**
     * Set colorants.
     *  
     * @param DictionaryObject $colorants
     * @return DeviceNColourSpaceAttributesDictionaryType
     */
    public function setColorants(DictionaryObject $colorants)
    {
        $this->setEntry('Colorants', $colorants);
        return $this;
    }

    /**
     * Set process.
     *  
     * @param DictionaryObject $process
     * @return DeviceNColourSpaceAttributesDictionaryType
     */
    public function setProcess(DictionaryObject $process)
    {
        $this->setEntry('Process', $process);
        return $this;
    }

    /**
     * Set mixing hints.
     *
     * @param DictionaryObject $colorants
     * @return DeviceNColourSpaceAttributesDictionaryType
     */
    public function setMixingHints(DictionaryObject $colorants)
    {
        $this->setEntry('MixingHints', $mixinghints);
        return $this;
    }
}