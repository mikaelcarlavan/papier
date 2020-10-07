<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class DeviceNMixingHintsDictionaryType extends DictionaryObject
{
    /**
     * Set solidities.
     *  
     * @param DictionaryObject $solidities
     * @return DeviceNMixingHintsDictionaryType
     */
    public function setSolidities(DictionaryObject $solidities)
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
    public function setPrintingOrder(ArrayObject $order)
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
    public function setDotGain(DictionaryObject $dotgain)
    {
        $this->setEntry('DotGain', $dotgain);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        if ($this->hasEntry('Solidities') && !$this->hasEntry('PrintingOrder')) {
            throw new RuntimeException("PrintingOrder is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }

}