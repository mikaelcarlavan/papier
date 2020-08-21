<?php

namespace Papier\Validator;

use Papier\Graphics\PaintType;
use Papier\Validator\IntValidator;

class PaintTypeValidator extends IntValidator
{
    /**
     * Paint types.
     *
     * @var array
     */
    const PAINT_TYPES = array(
        PaintType::COLOURED_TILING_PATTERN,
        PaintType::UNCOLOURED_TILING_PATTERN,
    );


     /**
     * Test if given parameter is a valid paint type.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::PAINT_TYPES);
        return $isValid;
    }
}