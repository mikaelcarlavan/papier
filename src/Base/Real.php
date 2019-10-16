<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\RealValidator;

use InvalidArgumentException;

class Real extends Object
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function setValue($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See Real class's documentation for possible values.");
        }

        return parent::setValue($value);
    }    
}