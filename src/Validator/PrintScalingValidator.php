<?php

namespace Papier\Validator;

use Papier\Document\PrintScaling;

class PrintScalingValidator extends StringValidator
{
    /**
     * Page scaling types.
     *
     * @var array<string>
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
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::PRINT_SCALING_TYPES);
    }
}