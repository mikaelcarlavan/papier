<?php

namespace Papier\Object\Base;

use Papier\Object\Base\IndirectObject;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class StringObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    * @return \Papier\Object\Base\StringObject
    */
    public function setValue($value)
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See String class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 
}