<?php

namespace Papier\Validator;

use Papier\Document\AnnotationIT;
use Papier\Document\AnnotationJustification;
use Papier\Validator\Base\Validator;

class AnnotationITValidator implements Validator
{
	/**
	 * Annotation justifications
	 *
	 * @var array<string>
	 */
	const ANNOTATION_ITS = array(
		AnnotationIT::FREE_TEXT,
		AnnotationIT::FREE_TEXT_CALLOUT,
		AnnotationIT::FREE_TEXT_TYPE_WRITER,
	);


	/**
	 * Test if given parameter is a valid type
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::ANNOTATION_ITS);
	}
}