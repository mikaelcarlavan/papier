<?php

namespace Papier\Validator;

use Papier\Document\AnnotationType;
use Papier\Document\BorderStyle;
use Papier\Validator\Base\Validator;

class BorderStyleValidator implements Validator
{
	/**
	 * Border styles
	 *
	 * @var array<string>
	 */
	const STYLES = array(
		BorderStyle::SOLID,
		BorderStyle::DASHED,
		BorderStyle::BEVELED,
		BorderStyle::INSET,
		BorderStyle::UNDERLINE,
	);


	/**
	 * Test if given parameter is a valid style
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::STYLES);
	}
}