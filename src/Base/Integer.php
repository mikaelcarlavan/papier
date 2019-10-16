<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\IntValidator;

use InvalidArgumentException;

class Integer extends Object
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function setValue($value)
    {
        if (!IntValidator::isValid($value)) {
            throw new InvalidArgumentException("Integer is incorrect. See Integer class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}