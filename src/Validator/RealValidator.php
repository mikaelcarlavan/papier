<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class RealValidator implements Validator
{
     /**
     * Test if given parameter is a valid real.
     * 
     * @param  mixed  $value
     * @param float|null $min
     * @param float|null $max
     * @return bool
     */
    public static function isValid($value, float $min = null, float $max = null): bool
    {
        $isValid = is_float($value);
        if (is_float($min) && $isValid) {
            $isValid = ($value >= $min);
        }  
        if (is_float($max) && $isValid) {
            $isValid = ($value <= $max);
        }             
        return $isValid;
    }
}