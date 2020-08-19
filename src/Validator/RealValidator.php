<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class RealValidator implements Validator
{
     /**
     * Test if given parameter is a valid real.
     * 
     * @param  mixed  $value
     * @param  float  $min
     * @param  float  $max
     * @return bool
     */
    public static function isValid($value, $min = null, $max = null)
    {
        $isValid = is_float($value);
        if (is_float($min) && $isValid) {
            $isValid = $isValid & ($value >= $min);
        }  
        if (is_float($max) && $isValid) {
            $isValid = $isValid & ($value <= $max);
        }             
        return $isValid;
    }
}