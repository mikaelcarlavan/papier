<?php

namespace Papier\Validator;

use Papier\Graphics\LineJoinStyle;

use Papier\Validator\Base\Validator;

class LineJoinStyleValidator implements Validator
{
    /**
     * Line join styles.
     *
     * @var array
     */
    const LINE_JOIN_STYLES = array(
        LineJoinStyle::MITER_JOIN,
        LineJoinStyle::ROUND_JOIN,
        LineJoinStyle::BEVEL_JOIN,
    );


     /**
     * Test if given parameter is a valid line join style.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value) && in_array($value, self::LINE_JOIN_STYLES);
    }
}