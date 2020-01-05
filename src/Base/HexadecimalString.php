<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class HexadecimalString extends Object
{
    /**
    * Set object's value.
    *
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    * @return \Papier\Base\HexadecimalString
    */
    public function setValue($value)
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See HexadecimalString class's documentation for possible values.");
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
        $chars = str_split($this->getValue());

        $value = '';
        if (is_array($chars)) {
            foreach ($chars as $char) {
                $value .= str_pad(dechex(ord($char)), 2, "0", STR_PAD_LEFT);
            }
        }

        return '<'.$value.'>';
    }
}