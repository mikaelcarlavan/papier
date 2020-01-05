<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\RealValidator;

use InvalidArgumentException;

class Real extends Object
{
    /**
    * Set object's value.
    *
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Base\Real
    */
    public function setValue($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See Real class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}