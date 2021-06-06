<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class IntegerValidator implements Validator
{
     /**
     * Test if given parameter is a valid integer.
     * 
     * @param  mixed  $value
     * @param int|null $min
     * @param int|null $max
     * @return bool
     */
    public static function isValid($value, int $min = null, int $max = null): bool
    {
        $isValid = is_int($value);
        if (is_int($min) && $isValid) {
            $isValid = ($value >= $min);
        }  
        if (is_int($max) && $isValid) {
            $isValid = ($value <= $max);
        }             
        return $isValid;
    }
}