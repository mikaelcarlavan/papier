<?php

namespace Papier\Validator;

use Papier\Document\PrintScaling;
use Papier\Validator\StringValidator;

class PrintScalingValidator extends StringValidator
{
    /**
     * Page scaling types.
     *
     * @var array
     */
    const PRINT_SCALING_TYPES = array(
        PrintScaling::NONE,
        PrintScaling::APP_DEFAULT,
    );


     /**
     * Test if given parameter is a valid page mode.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::PRINT_SCALING_TYPES);
        return $isValid;
    }
}