<?php

namespace Papier\Validator;

use Papier\Document\AnnotationType;
use Papier\Graphics\ColourComponents;
use Papier\Validator\Base\Validator;

class AnnotationTypeValidator implements Validator
{
	/**
	 * Annotation types
	 *
	 * @var array<string>
	 */
	const ANNOTATION_TYPES = array(
		AnnotationType::TEXT,
		AnnotationType::LINK,
		AnnotationType::FREE_TEXT,
		AnnotationType::LINE,
		AnnotationType::SQUARE,
		AnnotationType::CIRCLE,
		AnnotationType::POLYGON,
		AnnotationType::POLY_LINE,
		AnnotationType::HIGHLIGHT,
		AnnotationType::UNDERLINE,
		AnnotationType::SQUIGGLY,
		AnnotationType::STRIKE_OUT,
		AnnotationType::STAMP,
		AnnotationType::CARET,
		AnnotationType::INK,
		AnnotationType::POPUP,
		AnnotationType::FILE_ATTACHMENT,
		AnnotationType::SOUND,
		AnnotationType::MOVIE,
		AnnotationType::WIDGET,
		AnnotationType::SCREEN,
		AnnotationType::PRINTER_MARK,
		AnnotationType::TRAP_NET,
		AnnotationType::WATERMARK,
		AnnotationType::THREE_D,
		AnnotationType::REDACT,
	);


	/**
	 * Test if given parameter is a valid type
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::ANNOTATION_TYPES);
	}
}