<?php

namespace Papier\Type;

use Papier\Base\IndirectObject;
use Papier\Validator\NumberValidator;

use InvalidArgumentException;

class NumberType extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return NumberType
    */
    public function setValue($value): NumberType
    {
        if (!NumberValidator::isValid($value)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        parent::setValue($value);
        return $this;
    }    
}