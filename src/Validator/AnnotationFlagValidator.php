<?php

namespace Papier\Validator;

use Papier\Document\AnnotationFlag;
use Papier\Font\FontDescriptorFlag;
use Papier\Validator\Base\Validator;

class AnnotationFlagValidator implements Validator
{
	/**
	 * Flags.
	 *
	 * @var array<string>
	 */
	const ANNOTATION_FLAGS = array(
		AnnotationFlag::INVISIBLE,
		AnnotationFlag::HIDDEN,
		AnnotationFlag::PRINT,
		AnnotationFlag::NO_ZOOM,
		AnnotationFlag::NO_ROTATE,
		AnnotationFlag::NO_VIEW,
		AnnotationFlag::READ_ONLY,
		AnnotationFlag::LOCKED,
		AnnotationFlag::TOGGLE_NO_VIEW,
		AnnotationFlag::LOCKED_CONTENTS,
	);

	/**
	 * Test if given parameter is a valid function type.
	 *
	 * @param  int  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		$bit = 0;
		foreach (self::ANNOTATION_FLAGS as $flag) {
			$bit |= ($value & $flag);
		}

		return IntegerValidator::isValid($value) && $bit > 0;
	}
}