<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class BooleanValidator implements Validator
{
     /**
     * Test if given parameter is a valid bool.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return is_bool($value);
    }
}