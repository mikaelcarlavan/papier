<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class RealValidator implements Validator
{
     /**
     * Test if given parameter is a valid real.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = is_float($value);
        return $isValid;
    }
}