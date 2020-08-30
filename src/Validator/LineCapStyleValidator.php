<?php

namespace Papier\Validator;

use Papier\Graphics\LineCapStyle;

use Papier\Validator\Base\Validator;
use Papier\Validator\IntegerValidator;

class LineCapStyleValidator implements Validator
{
    /**
     * Line cap styles.
     *
     * @var array
     */
    const LINE_CAP_STYLES = array(
        LineCapStyle::BUTT_CAP,
        LineCapStyle::ROUND_CAP,
        LineCapStyle::PROJECTING_SQUARE_CAP,
    );


     /**
     * Test if given parameter is a valid line cap style.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = IntegerValidator::isValid($value) && in_array($value, self::LINE_CAP_STYLES);
        return $isValid;
    }
}