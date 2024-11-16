<?php

namespace Papier\Validator;

use Papier\Validator\Base\Validator;

class BitsPerSampleValidator implements Validator
{
    /**
     * Bits per sample allowed values.
     *
     * @var array<int>
     */
    const BITS_PER_SAMPLE = array(
        1, 2, 4, 8, 12, 16, 24, 32
    );


     /**
     * Test if given parameter is a valid bits per sample.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value) && in_array($value, self::BITS_PER_SAMPLE);
    }
}