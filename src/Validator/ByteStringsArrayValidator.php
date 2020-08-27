<?php

namespace Papier\Validator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\ByteStringValidator;

class ByteStringsArrayValidator extends ArrayValidator
{
     /**
     * Test if given parameter is a valid array of byte strings.
     * 
     * @param  mixed  $value
     * @param  int  $size
     * @return bool
     */
    public static function isValid($value, $size = -1)
    {
        $isValid = parent::isValid($value, $size);

        if ($isValid) {
            foreach ($value as $string) {
                $isValid = $isValid & ByteStringValidator::isValid($string);
            }
        }

        return $isValid;
    }
}