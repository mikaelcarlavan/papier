<?php

namespace Papier\Validator;

use Papier\Font\FontStretch;
use Papier\Graphics\DeviceColourSpace;

class FontStretchValidator extends StringValidator
{
	/**
	 * Font stretches.
	 *
	 * @var array<string>
	 */
	const FONT_STRETCHES = array(
		FontStretch::ULTRA_CONDENSED,
		FontStretch::EXTRA_CONDENSED,
		FontStretch::SEMI_CONDENSED,
		FontStretch::CONDENSED,
		FontStretch::NORMAL,
		FontStretch::SEMI_EXPANDED,
		FontStretch::EXPANDED,
		FontStretch::ULTRA_EXPANDED,
	);


	/**
	 * Test if given parameter is a valid device colour space.
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return parent::isValid($value) && in_array($value, self::FONT_STRETCHES);
	}
}