<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Validator\IntegerValidator;

use InvalidArgumentException;

class IntegerObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param mixed $value
    * @return IntegerObject
    * @throws InvalidArgumentException if the provided argument is not of type 'int'.
    */
    public function setValue(mixed $value): IntegerObject
    {
        if (!IntegerValidator::isValid($value)) {
            throw new InvalidArgumentException("Integer is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        parent::setValue($value);
        return $this;
    }    
}