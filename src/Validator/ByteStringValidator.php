<?php

namespace Papier\Validator;

use Papier\Type\DeveloperExtensionDictionaryType;

class ByteStringValidator extends StringValidator
{
     /**
     * Test if given parameter is a valid byte string.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        $isValid = parent::isValid($value);

        if ($isValid) {
			/** @var string $value */
			$isValid = strlen($value) == 1;
        }

        return $isValid;
    }
}