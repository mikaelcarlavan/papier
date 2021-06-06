<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class ArrayValidator implements Validator
{
     /**
     * Test if given parameter is a valid array.
     * 
     * @param  mixed  $value
     * @param int $size
     * @return bool
     */
    public static function isValid($value, int $size = -1): bool
    {
        $isValid = is_array($value);

        if ($size > 0) {
            $isValid = $isValid & (count($value) == $size);
        }

        return $isValid;
    }
}