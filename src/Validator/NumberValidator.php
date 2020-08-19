<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

use Papier\Validator\IntValidator;
use Papier\Validator\RealValidator;

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
    public static function isValid($value, $min = null, $max = null)
    {
        $isValid = RealValidator::isValid($value, $min, $max) || IntValidator::isValid($value, $min, $max);
        return $isValid;
    }
}