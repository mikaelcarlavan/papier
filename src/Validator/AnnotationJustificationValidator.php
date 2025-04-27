<?php

namespace Papier\Validator;

use Papier\Document\AnnotationJustification;
use Papier\Document\AnnotationType;
use Papier\Validator\Base\Validator;

class AnnotationJustificationValidator implements Validator
{
	/**
	 * Annotation justifications
	 *
	 * @var array<int>
	 */
	const ANNOTATION_JUSTIFICATIONS = array(
		AnnotationJustification::LEFT_JUSTIFIED,
		AnnotationJustification::CENTERED,
		AnnotationJustification::RIGHT_JUSTIFIED,
	);


	/**
	 * Test if given parameter is a valid type
	 *
	 * @param  int  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return IntegerValidator::isValid($value) && in_array($value, self::ANNOTATION_JUSTIFICATIONS);
	}
}