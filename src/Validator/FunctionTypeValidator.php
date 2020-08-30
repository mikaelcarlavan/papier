<?php

namespace Papier\Validator;

use Papier\Functions\FunctionType;

use Papier\Validator\Base\Validator;
use Papier\Validator\IntegerValidator;

class FunctionTypeValidator implements Validator
{
    /**
     * Function types.
     *
     * @var array
     */
    const FUNCTION_TYPES = array(
        FunctionType::SAMPLED,
        FunctionType::EXPONENTIAL_INTERPOLATION,
        FunctionType::STITCHING,
        FunctionType::POSTSCRIPT_CALCULATOR,
    );


     /**
     * Test if given parameter is a valid function type.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = IntegerValidator::isValid($value) && in_array($value, self::FUNCTION_TYPES);
        return $isValid;
    }
}