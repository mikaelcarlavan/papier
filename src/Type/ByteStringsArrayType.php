<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\ArrayType;
use Papier\Validator\ByteStringsArrayValidator;

class ByteStringsArrayType extends ArrayType
{
	/**
	 * Set object's byte strings.
	 *
	 * @param mixed $value
	 * @return ByteStringsArrayType
	 * @throws InvalidArgumentException if the provided argument is not an array of 'string'.
	 */
	public function setValue(mixed $value): ByteStringsArrayType
	{
		if (!ByteStringsArrayValidator::isValid($value)) {
			throw new InvalidArgumentException("Array is incorrect. See " . __CLASS__ . " class's documentation for possible values.");
		}

		/** @var ByteStringsArrayType $value */
		$objects = $this->getObjects();

		foreach ($value as $i => $val) {
			// Each element is an individual byte string
			$object = Factory::create('Papier\Type\Base\ByteStringType', $val);
			$objects[$i] = $object;
		}

		parent::setValue($objects);
		return $this;
	}

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return ByteStringsArrayType
	 */
	public static function fromString(string $data): ByteStringsArrayType
	{
		// Parse array using parent ArrayType parser
		/** @var ArrayType $arrayObject */
		$arrayObject = parent::fromString($data);

		// Convert each item to a ByteStringType
		$objects = $arrayObject->getObjects();
		$converted = [];

		foreach ($objects as $i => $obj) {
			$value = is_object($obj) && method_exists($obj, 'getValue')
				? $obj->getValue()
				: (string) $obj;

			$converted[$i] = Factory::create('Papier\Type\ByteStringType', $value);
		}

		/** @var ByteStringsArrayType $instance */
		$object = Factory::create('Papier\Type\ByteStringsArrayType');
		$object->setValue($converted);

		return $object;
	}
}