<?php

namespace Papier\Type;

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
     * @param  \Papier\Object\DictionaryObject  $solidities
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DeviceNMixingHintsDictionaryType
     */
    public function setSolidities($solidities)
    {
        if (!$solidities instanceof DictionaryObject) {
            throw new InvalidArgumentException("Solidities is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Solidities', $solidities);
        return $this;
    } 

    /**
     * Set printing order.
     *  
     * @param  \Papier\Object\ArrayObject  $order
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\DeviceNMixingHintsDictionaryType
     */
    public function setPrintingOrder($order)
    {
        if (!$order instanceof ArrayObject) {
            throw new InvalidArgumentException("PrintingOrder is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('PrintingOrder', $order);
        return $this;
    }

    /**
     * Set dot gain.
     *  
     * @param  \Papier\Object\DictionaryObject  $dotgain
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DeviceNMixingHintsDictionaryType
     */
    public function setDotGain($dotgain)
    {
        if (!$dotgain instanceof DictionaryObject) {
            throw new InvalidArgumentException("DotGain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

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