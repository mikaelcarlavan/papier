<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class BoolValidator implements Validator
{
     /**
     * Test if given parameter is a valid bool.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = is_bool($value);
        return $isValid;
    }
}