<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;

use Papier\Type\Base\DictionaryType;
use RuntimeException;

class DeviceNMixingHintsDictionaryType extends DictionaryType
{
    /**
     * Set solidities.
     *  
     * @param DictionaryObject $solidities
     * @return DeviceNMixingHintsDictionaryType
     */
    public function setSolidities(DictionaryObject $solidities): DeviceNMixingHintsDictionaryType
    {
        $this->setEntry('Solidities', $solidities);
        return $this;
    } 

    /**
     * Set printing order.
     *  
     * @param  ArrayObject  $order
     * @return DeviceNMixingHintsDictionaryType
     */
    public function setPrintingOrder(ArrayObject $order): DeviceNMixingHintsDictionaryType
    {
        $this->setEntry('PrintingOrder', $order);
        return $this;
    }

    /**
     * Set dot gain.
     *  
     * @param DictionaryObject $dotgain
     * @return DeviceNMixingHintsDictionaryType
     */
    public function setDotGain(DictionaryObject $dotgain): DeviceNMixingHintsDictionaryType
    {
        $this->setEntry('DotGain', $dotgain);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        if ($this->hasEntry('Solidities') && !$this->hasEntry('PrintingOrder')) {
            throw new RuntimeException("PrintingOrder is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }

}