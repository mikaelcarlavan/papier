<?php

namespace Papier\Validator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\NumberValidator;

class NumbersArrayValidator extends ArrayValidator
{
     /**
     * Test if given parameter is a valid array of numbers.
     * 
     * @param  mixed  $value
     * @param  int  $size
     * @return bool
     */
    public static function isValid($value, $size = -1)
    {
        $isValid = parent::isValid($value, $size);

        if ($isValid) {
            foreach ($value as $number) {
                $isValid = $isValid & NumberValidator::isValid($number);
            }
        }

        return $isValid;
    }
}