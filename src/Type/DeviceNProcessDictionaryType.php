<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class DeviceNProcessDictionaryType extends DictionaryObject
{
    /**
     * Set color space.
     *  
     * @param  mixed  $space
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return \Papier\Type\DeviceNProcessDictionaryType
     */
    public function setColorSpace($space)
    {
        if (!StringValidator::isValid($space) && !$space instanceof ArrayObject) {
            throw new InvalidArgumentException("ColorSpace is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $space instanceof ArrayObject ? $space : Factory::create('Name', $space);

        $this->setEntry('ColorSpace', $value);
        return $this;
    } 

    /**
     * Set components.
     *  
     * @param  \Papier\Object\ArrayObject  $components
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\DeviceNProcessDictionaryType
     */
    public function setComponents($components)
    {
        if (!$components instanceof ArrayObject) {
            throw new InvalidArgumentException("Components is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Components', $components);
        return $this;
    }
}