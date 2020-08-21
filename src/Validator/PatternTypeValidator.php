<?php

namespace Papier\Validator;

use Papier\Graphics\PatternType;
use Papier\Validator\IntValidator;

class PatternTypeValidator extends IntValidator
{
    /**
     * Pattern types.
     *
     * @var array
     */
    const PATTERN_TYPES = array(
        PatternType::TILING_PATTERN,
        PatternType::SHADING_PATERN,
    );


     /**
     * Test if given parameter is a valid pattern type.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::PATTERN_TYPES);
        return $isValid;
    }
}