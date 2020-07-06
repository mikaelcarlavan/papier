<?php

namespace Papier\Validator;
use Papier\Validator\IntValidator;

class VersionValidator implements IntValidator
{
    /**
     * Minimum allowable value of header's version.
     *
     * @var int
     */
    private $minVersion = 0;

    /**
     * Maximal allowable value of header's version.
     *
     * @var int
     */
    private $maxVersion = 7;

     /**
     * Test if given parameter is a valid bool.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value, $minVersion, $maxVersion);
        return $isValid;
    }
}