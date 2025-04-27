<?php

namespace Papier\Validator;

use Papier\Document\AnnotationFlag;
use Papier\Document\AnnotationHighlightMode;
use Papier\Validator\Base\Validator;

class AnnotationHighlightModeValidator implements Validator
{
	/**
	 * Modes.
	 *
	 * @var array<string>
	 */
	const ANNOTATION_HIGHLIGHT_MODES = array(
		AnnotationHighlightMode::INVERT,
		AnnotationHighlightMode::OUTLINE,
		AnnotationHighlightMode::NONE,
		AnnotationHighlightMode::PUSH,
	);

	/**
	 * Test if given parameter is a valid annotation highlight mode.
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::ANNOTATION_HIGHLIGHT_MODES);
	}
}