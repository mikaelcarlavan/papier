<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

use DateTime;

class DateValidator implements Validator
{
     /**
     * Test if given parameter is a valid date.
     * 
     * @param  mixed $value
     * @return bool
     */
    public static function isValid($value): bool
    {
		$isValid = false;
        if ($value instanceof DateTime) {
            $isValid = true;
        } elseif (is_string($value)) {
            $isValid = strtotime($value) !== false;
        }
        
        return $isValid;
    }
}