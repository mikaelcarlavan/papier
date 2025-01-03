<?php

namespace Papier\Helpers;

use Papier\Factory\Factory;
use Papier\Papier;
use Papier\Type\AnnotationDictionaryType;
use Papier\Validator\NumbersArrayValidator;
use InvalidArgumentException;

class MetricHelper
{
	/**
	 * Convert to user-unit
	 *
	 * @param mixed $value
	 * @return array|int|float
	 */
	public static function toUserUnit(mixed $value): array|int|float
	{
		$mmToUserUnit = Papier::MM_TO_USER_UNIT;

		if (is_array($value)) {
			$value = array_map(function ($item) use ($mmToUserUnit) {
				return $item * $mmToUserUnit;
			}, $value);
		} else if (is_numeric($value)) {
			$value = $mmToUserUnit * $value;
		} else {
			throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		return $value;
	}
}