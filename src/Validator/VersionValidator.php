<?php

namespace Papier\Validator;

use Papier\Validator\Base\Validator;

class VersionValidator implements Validator
{
    /**
     * Minimum allowable value of header's version.
     *
     * @var int
     */
    const minVersion = 0;

    /**
     * Maximal allowable value of header's version.
     *
     * @var int
     */
    const maxVersion = 7;

     /**
     * Test if given parameter is a valid bool.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value, self::minVersion, self::maxVersion);
    }
}