<?php

namespace Papier\Validator;

class NumbersArrayValidator extends ArrayValidator
{
     /**
     * Test if given parameter is a valid array of 'float' or 'int'.
     * 
     * @param  mixed  $value
     * @param int $size
     * @return bool
     */
    public static function isValid($value, int $size = -1): bool
    {
        $isValid = parent::isValid($value, $size);

        if ($isValid) {
            foreach ($value as $number) {
                $isValid = $isValid && NumberValidator::isValid($number);
            }
        }

        return $isValid;
    }
}