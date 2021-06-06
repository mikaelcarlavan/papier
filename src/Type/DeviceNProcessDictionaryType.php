<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class DeviceNProcessDictionaryType extends DictionaryObject
{
    /**
     * Set color space.
     *  
     * @param  mixed  $space
     * @return DeviceNProcessDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     */
    public function setColorSpace($space): DeviceNProcessDictionaryType
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
     * @param  ArrayObject  $components
     * @return DeviceNProcessDictionaryType
     */
    public function setComponents(ArrayObject $components): DeviceNProcessDictionaryType
    {
        $this->setEntry('Components', $components);
        return $this;
    }
}