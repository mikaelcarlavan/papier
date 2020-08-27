<?php

namespace Papier\Validator;

use Papier\Validator\StringValidator;

class ByteStringValidator extends StringValidator
{
     /**
     * Test if given parameter is a valid byte string.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value);

        if ($isValid) {
            $isValid = $isValid & strlen($value) == 1;
        }

        return $isValid;
    }
}