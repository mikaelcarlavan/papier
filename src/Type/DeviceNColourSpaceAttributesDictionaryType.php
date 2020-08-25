<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class DeviceNColourSpaceAttributesDictionaryType extends DictionaryObject
{
    /**
     * Set subtype.
     *  
     * @param  string  $subtype
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DeviceNColourSpaceAttributesDictionaryType
     */
    public function setSubtype($subtype)
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
     * @param  \Papier\Object\DictionaryObject  $colorants
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DeviceNColourSpaceAttributesDictionaryType
     */
    public function setColorants($colorants)
    {
        if (!$colorants instanceof DictionaryObject) {
            throw new InvalidArgumentException("Mask is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Colorants', $colorants);
        return $this;
    }

    /**
     * Set process.
     *  
     * @param  \Papier\Object\DictionaryObject  $process
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DeviceNColourSpaceAttributesDictionaryType
     */
    public function setProcess($process)
    {
        if (!$process instanceof DictionaryObject) {
            throw new InvalidArgumentException("Process is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Process', $process);
        return $this;
    }

    /**
     * Set mixing hints.
     *  
     * @param  \Papier\Object\DictionaryObject  $mixinghints
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DeviceNColourSpaceAttributesDictionaryType
     */
    public function setMixingHints($colorants)
    {
        if (!$mixinghints instanceof DictionaryObject) {
            throw new InvalidArgumentException("MixingHints is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('MixingHints', $mixinghints);
        return $this;
    }
}