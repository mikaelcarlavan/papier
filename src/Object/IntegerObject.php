<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Validator\IntValidator;

use InvalidArgumentException;

class IntegerObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'int'.
    * @return \Papier\Object\IntegerObject
    */
    public function setValue($value)
    {
        if (!IntValidator::isValid($value)) {
            throw new InvalidArgumentException("Integer is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}