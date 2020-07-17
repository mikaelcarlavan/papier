<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class ArrayValidator implements Validator
{
     /**
     * Test if given parameter is a valid string.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = is_array($value);
        return $isValid;
    }
}