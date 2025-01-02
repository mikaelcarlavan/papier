<?php

namespace Papier\Validator;

use Papier\Document\BorderEffect;
use Papier\Document\RelationshipType;
use Papier\Validator\Base\Validator;

class RelationshipTypeValidator implements Validator
{
	/**
	 * Types
	 *
	 * @var array<string>
	 */
	const TYPES = array(
		RelationshipType::REPLY,
		RelationshipType::GROUP,
	);


	/**
	 * Test if given parameter is a valid type
	 *
	 * @param  string  $value
	 * @return bool
	 */
	public static function isValid($value): bool
	{
		return StringValidator::isValid($value) && in_array($value, self::TYPES);
	}
}