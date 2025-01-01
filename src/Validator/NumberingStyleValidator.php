<?php

namespace Papier\Validator;

use Papier\Document\NumberingStyle;
use Papier\Graphics\ColourComponents;

class NumberingStyleValidator
{
	/**
	 * Numbering style allowed values.
	 *
	 * @var array<string>
	 */
	const NUMBERING_STYLES = array(
		NumberingStyle::DECIMAL_ARABIC,
		NumberingStyle::UPPERCASE_LETTERS,
		NumberingStyle::LOWERCASE_LETTERS,
		NumberingStyle::UPPERCASE_ROMAN,
		NumberingStyle::LOWERCASE_ROMAN
	);


	/**
	 * Test if given parameter is a valid numbering style.
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::NUMBERING_STYLES);
	}
}