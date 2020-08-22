<?php

namespace Papier\Validator;

use Papier\Graphics\OverprintMode;
use Papier\Validator\IntegerValidator;

class OverprintModeValidator extends IntegerValidator
{
    /**
     * Overprint modes.
     *
     * @var array
     */
    const OVERPRINT_MODES = array(
        OverprintMode::NONZERO_MODE,
        OverprintMode::ZERO_MODE,
    );


     /**
     * Test if given parameter is a valid overprint mode.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::OVERPRINT_MODES);
        return $isValid;
    }
}