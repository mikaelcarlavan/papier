<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class StringObject extends Object
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    * @return \Papier\Base\StringObject
    */
    public function setValue($value)
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See String class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 
}