<?php

namespace Papier\Validator;

use Papier\Document\Trapped;

class TrappedValidator extends StringValidator
{
    /**
     * Trapped values.
     *
     * @var array
     */
    const TRAPPED_VALUES = array(
        Trapped::TRUE,
        Trapped::FALSE,
        Trapped::UNKNOWN,
    );


     /**
     * Test if given parameter is a valid trapped value.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::TRAPPED_VALUES);
    }
}