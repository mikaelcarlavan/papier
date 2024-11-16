<?php

namespace Papier\Validator;

use Papier\Graphics\PaintType;

use Papier\Validator\Base\Validator;

class PaintTypeValidator implements Validator
{
    /**
     * Paint types.
     *
     * @var array<int>
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
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value) && in_array($value, self::PAINT_TYPES);
    }
}