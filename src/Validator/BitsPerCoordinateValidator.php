<?php

namespace Papier\Validator;

use Papier\Validator\Base\Validator;

class BitsPerCoordinateValidator implements Validator
{
    /**
     * Bits per coordinate allowed values.
     *
     * @var array<int>
     */
    const BITS_PER_COORDINATEE = array(
        1, 2, 4, 8, 12, 16, 24, 32
    );


     /**
     * Test if given parameter is a valid bits per coordinate.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value) && in_array($value, self::BITS_PER_COORDINATEE);
    }
}