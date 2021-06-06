<?php

namespace Papier\Validator;

use Papier\Text\Encoding;

class EncodingValidator extends StringValidator
{
    /**
     * Encodings.
     *
     * @var array
     */
    const ENCODINGS = array(
        Encoding::MAC_ROMAN,
        Encoding::MAC_EXPERT,
        Encoding::WIN_ANSI,
    );


     /**
     * Test if given parameter is a valid device colour space.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::ENCODINGS);
    }
}