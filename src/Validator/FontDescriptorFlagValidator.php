<?php

namespace Papier\Validator;

use Papier\Font\FontDescriptorFlag;
use Papier\Text\Encoding;
use Papier\Validator\Base\Validator;

class FontDescriptorFlagValidator implements Validator
{
	/**
	 * Flags.
	 *
	 * @var array<string>
	 */
	const FLAGS = array(
		FontDescriptorFlag::FIXED_PITCH,
		FontDescriptorFlag::SERIF,
		FontDescriptorFlag::SYMBOLIC,
		FontDescriptorFlag::SCRIPT,
		FontDescriptorFlag::NON_SYMBOLIC,
		FontDescriptorFlag::ITALIC,
		FontDescriptorFlag::ALL_CAP,
		FontDescriptorFlag::SMALL_CAP,
		FontDescriptorFlag::FORCE_BOLD,
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
		foreach (self::FLAGS as $flag) {
			$bit |= ($value & $flag);
		}

		return IntegerValidator::isValid($value) && $bit > 0;
	}
}