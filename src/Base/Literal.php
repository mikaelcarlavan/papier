<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class Literal extends Object
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function setValue($value)
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See Literal class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 
    

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $value = $this->getValue();
        return '('.$value.')';
    }
}