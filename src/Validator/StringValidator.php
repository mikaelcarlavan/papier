<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class StringValidator implements Validator
{
     /**
     * Test if given parameter is a valid string.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return is_string($value);
    }
}