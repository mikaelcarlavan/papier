<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class StringValidator implements Validator
{
     /**
     * Test if given string is a valid string.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = is_string($value);
        return $isValid;
    }
}