<?php

namespace Papier\Validator;

use Papier\Document\BorderEffect;
use Papier\Document\BorderStyle;
use Papier\Validator\Base\Validator;

class BorderEffectValidator implements Validator
{
	/**
	 * Border effects
	 *
	 * @var array<string>
	 */
	const EFFECTS = array(
		BorderEffect::NO_EFFECT,
		BorderEffect::CLOUDY,
	);


	/**
	 * Test if given parameter is a valid effect
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::EFFECTS);
	}
}