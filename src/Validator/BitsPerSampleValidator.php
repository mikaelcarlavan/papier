<?php

namespace Papier\Validator;

use Papier\Functions\FunctionType;
use Papier\Validator\IntValidator;

class BitsPerSampleValidator extends IntValidator
{
    /**
     * Bits per sample allowed values.
     *
     * @var array
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
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::BITS_PER_SAMPLE);
        return $isValid;
    }
}