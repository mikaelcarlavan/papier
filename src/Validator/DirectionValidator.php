<?php

namespace Papier\Validator;

use Papier\Document\Direction;
use Papier\Validator\StringValidator;

class DirectionValidator extends StringValidator
{
    /**
     * Directions.
     *
     * @var array
     */
    const DIRECTIONS = array(
        Direction::LEFT_TO_RIGHT,
        Direction::RIGHT_TO_LEFT,
    );


     /**
     * Test if given parameter is a valid direction.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::DIRECTIONS);
        return $isValid;
    }
}