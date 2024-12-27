<?php

namespace Papier\Validator;

use Papier\Font\FontStretch;
use Papier\Validator\Base\Validator;

class FontWeightValidator implements Validator
{
	/**
	 * Font weights.
	 *
	 * @var array<int>
	 */
	const FONT_WEIGHTS = array(
		100,
		200,
		300,
		400,
		500,
		600,
		700,
		800,
		900
	);


	/**
	 * Test if given parameter is a valid bits per component.
	 *
	 * @param  int  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return IntegerValidator::isValid($value) && in_array($value, self::FONT_WEIGHTS);
	}
}