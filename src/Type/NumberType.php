<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Object\IndirectObject;
use Papier\Validator\NumberValidator;

class NumberType extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param mixed $value
    * @return NumberType
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    */
    public function setValue(mixed $value): NumberType
    {
        if (!NumberValidator::isValid($value)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        parent::setValue($value);
        return $this;
    }    
}