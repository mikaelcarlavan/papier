<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

use DateTime;

class DateValidator implements Validator
{
     /**
     * Test if given parameter is a valid date.
     * 
     * @param  DateTime|string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        if ($value instanceof DateTime) {
            $isValid = true;
        } else {
            $isValid = strtotime($value) !== false;
        }
        
        return $isValid;
    }
}