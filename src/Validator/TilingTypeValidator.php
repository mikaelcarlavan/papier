<?php

namespace Papier\Validator;

use Papier\Graphics\TilingType;
use Papier\Validator\IntValidator;

class TilingTypeValidator extends IntValidator
{
    /**
     * Tiling types.
     *
     * @var array
     */
    const TILING_TYPES = array(
        TilingType::CONSTANT_SPACING,
        TilingType::NO_DISTORTION,
        TilingType::CONSTANT_SPACING_AND_FASTER_TILING,
    );


     /**
     * Test if given parameter is a valid paint type.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::TILING_TYPES);
        return $isValid;
    }
}