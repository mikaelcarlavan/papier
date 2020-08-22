<?php

namespace Papier\Validator;

use Papier\Validator\IntValidator;

class BitsPerFlagValidator extends IntValidator
{
    /**
     * Bits per flag allowed values.
     *
     * @var array
     */
    const BITS_PER_FLAG = array(
        2, 4, 8
    );


     /**
     * Test if given parameter is a valid bits per flag.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::BITS_PER_FLAG);
        return $isValid;
    }
}