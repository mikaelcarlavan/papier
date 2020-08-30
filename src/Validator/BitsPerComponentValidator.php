<?php

namespace Papier\Validator;

use Papier\Validator\Base\Validator;
use Papier\Validator\IntegerValidator;

class BitsPerComponentValidator implements Validator
{
    /**
     * Bits per component allowed values.
     *
     * @var array
     */
    const BITS_PER_COMPONENT = array(
        1, 2, 4, 8, 12, 16
    );


     /**
     * Test if given parameter is a valid bits per component.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = IntegerValidator::isValid($value) && in_array($value, self::BITS_PER_COMPONENT);
        return $isValid;
    }
}