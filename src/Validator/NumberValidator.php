<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class NumberValidator implements Validator
{
     /**
     * Test if given parameter is a valid real.
     * 
     * @param  mixed  $value
     * @param  mixed  $min
     * @param  mixed  $max
     * @return bool
     */
    public static function isValid($value, $min = null, $max = null): bool
    {
        return RealValidator::isValid($value, $min, $max) || IntegerValidator::isValid($value, $min, $max);
    }
}