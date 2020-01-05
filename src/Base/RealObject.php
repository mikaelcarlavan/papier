<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\RealValidator;

use InvalidArgumentException;

class RealObject extends Object
{
    /**
    * Set object's value.
    *
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Base\RealObject
    */
    public function setValue($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See RealObject class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}