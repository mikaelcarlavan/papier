<?php

namespace Papier\Validator;

class ByteStringValidator extends StringValidator
{
     /**
     * Test if given parameter is a valid byte string.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        $isValid = parent::isValid($value);

        if ($isValid) {
            $isValid =  strlen($value) == 1;
        }

        return $isValid;
    }
}