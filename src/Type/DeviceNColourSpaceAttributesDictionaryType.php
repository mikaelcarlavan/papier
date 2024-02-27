<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

class DeviceNColourSpaceAttributesDictionaryType extends DictionaryObject
{
    /**
     * Set subtype.
     *  
     * @param  string  $subtype
     * @return DeviceNColourSpaceAttributesDictionaryType
     */
    public function setSubtype(string $subtype): DeviceNColourSpaceAttributesDictionaryType
    {
        $value = Factory::create('Papier\Type\NameType', $subtype);

        $this->setEntry('Subtype', $value);
        return $this;
    } 

    /**
     * Set colorants.
     *  
     * @param DictionaryObject $colorants
     * @return DeviceNColourSpaceAttributesDictionaryType
     */
    public function setColorants(DictionaryObject $colorants): DeviceNColourSpaceAttributesDictionaryType
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
    public function setProcess(DictionaryObject $process): DeviceNColourSpaceAttributesDictionaryType
    {
        $this->setEntry('Process', $process);
        return $this;
    }

    /**
     * Set mixing hints.
     *
     * @param DictionaryObject $mixinghints
     * @return DeviceNColourSpaceAttributesDictionaryType
     */
    public function setMixingHints(DictionaryObject $mixinghints): DeviceNColourSpaceAttributesDictionaryType
    {
        $this->setEntry('MixingHints', $mixinghints);
        return $this;
    }
}