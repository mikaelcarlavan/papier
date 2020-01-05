<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class LiteralString extends Object
{
    /**
    * Set object's value.
    *
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    * @return \Papier\Base\LiteralString
    */
    public function setValue($value)
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See LiteralString class's documentation for possible values.");
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

        $trans = array('(' => '\(', ')' => '\)', '\\' => '\\\\');
        $value = strtr($value, $trans);

        return '('.$value.')';
    }
}