<?php

namespace Papier\Validator;

use Papier\Document\Duplex;

class DuplexValidator extends StringValidator
{
    /**
     * Duplex types.
     *
     * @var array
     */
    const DUPLEX_TYPES = array(
        Duplex::SIMPLEX,
        Duplex::DUPLEX_FLIP_SHORT_EDGE,
        Duplex::DUPLEX_FLIP_LONG_EDGE
    );


     /**
     * Test if given parameter is a valid page mode.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::DUPLEX_TYPES);
    }
}