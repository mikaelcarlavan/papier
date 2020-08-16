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
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = RealValidator::isValid($value) || IntValidator::isValid($value);
        return $isValid;
    }
}