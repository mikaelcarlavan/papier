<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\IntValidator;

use InvalidArgumentException;

class Integer extends Object
{
    /**
    * Set object's value.
    *
    * @throws InvalidArgumentException if the provided argument is not of type 'int'.
    * @return \Papier\Base\Integer
    */
    public function setValue($value)
    {
        if (!IntValidator::isValid($value)) {
            throw new InvalidArgumentException("Integer is incorrect. See Integer class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}