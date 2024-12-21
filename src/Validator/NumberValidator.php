<?php

namespace Papier\Validator;
use Papier\Validator\Base\Validator;

class NumberValidator implements Validator
{
     /**
     * Test if given parameter is a valid real.
     * 
     * @param  mixed  $value
     * @param mixed|null $min
     * @param mixed|null $max
     * @return bool
     */
    public static function isValid($value, mixed $min = null, mixed $max = null): bool
    {
		$isValid = is_numeric($value);
		if (is_numeric($min) && $isValid) {
			$isValid = ($value >= $min);
		}
		if (is_numeric($max) && $isValid) {
			$isValid = ($value <= $max);
		}
		return $isValid;
	}
}