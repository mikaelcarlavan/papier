<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class IntValidator implements Validator
{
     /**
     * Test if given string is a valid integer.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = is_int($value);         
        return $isValid;
    }
}