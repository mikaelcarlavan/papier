<?php

namespace Papier\Object;

use Papier\Object\Base\IndirectObject;
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
            throw new InvalidArgumentException("Integer is incorrect. See IntegerObject class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}