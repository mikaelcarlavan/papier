<?php

namespace Papier\Validator;

use Papier\Document\AnnotationHighlightMode;
use Papier\Document\LineEndingStyle;
use Papier\Validator\Base\Validator;

class LineEndingStyleValidator implements Validator
{
	/**
	 * Styles.
	 *
	 * @var array<string>
	 */
	const LINE_ENDING_STYLES = array(
		LineEndingStyle::NONE,
		LineEndingStyle::CLOSED_ARROW,
		LineEndingStyle::OPEN_ARROW,
		LineEndingStyle::R_CLOSED_ARROW,
		LineEndingStyle::R_OPEN_ARROW,
		LineEndingStyle::CIRCLE,
		LineEndingStyle::BUTT,
		LineEndingStyle::DIAMOND,
		LineEndingStyle::SLASH,
		LineEndingStyle::SQUARE,
	);

	/**
	 * Test if given parameter is a valid annotation highlight mode.
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::LINE_ENDING_STYLES);
	}
}