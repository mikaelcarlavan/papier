<?php

namespace Papier\Object;

use Papier\Base\Object;
use Papier\Validator\IntValidator;

use InvalidArgumentException;

class IntegerObject extends Object
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