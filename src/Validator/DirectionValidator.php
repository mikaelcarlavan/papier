<?php

namespace Papier\Validator;

use Papier\Document\Direction;

class DirectionValidator extends StringValidator
{
    /**
     * Directions.
     *
     * @var array<string>
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
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::DIRECTIONS);
    }
}