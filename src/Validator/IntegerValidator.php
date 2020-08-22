<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class IntegerValidator implements Validator
{
     /**
     * Test if given parameter is a valid integer.
     * 
     * @param  mixed  $value
     * @param  int  $min
     * @param  int  $max
     * @return bool
     */
    public static function isValid($value, $min = null, $max = null)
    {
        $isValid = is_int($value);
        if (is_int($min) && $isValid) {
            $isValid = $isValid & ($value >= $min);
        }  
        if (is_int($max) && $isValid) {
            $isValid = $isValid & ($value <= $max);
        }             
        return $isValid;
    }
}