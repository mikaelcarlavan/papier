<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\ArrayType;
use Papier\Validator\NumbersArrayValidator;

class NumbersArrayType extends ArrayType
{
    /**
     * Set object's numbers.
     *
     * @param mixed $value
     * @return NumbersArrayType
     */
    public function setValue(mixed $value): NumbersArrayType
    {
        if (!NumbersArrayValidator::isValid($value)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

		/** @var array<mixed> $value */
		foreach ($value as $i => $val) {
			$object = Factory::create('Papier\Type\NumberType', $val);
            $objects[$i] = $object;
        }

        parent::setValue($objects);
        return $this;
    }

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return NumbersArrayType
	 */
	public static function fromString(string $data): NumbersArrayType
	{
		// Start from parent ArrayType parser
		/** @var ArrayType $arrayObject */
		$arrayObject = parent::fromString($data);

		// Get parsed objects (likely as raw strings or generic base objects)
		$objects = $arrayObject->getObjects();
		$converted = [];

		foreach ($objects as $i => $obj) {
			$value = is_object($obj) && method_exists($obj, 'getValue')
				? $obj->getValue()
				: (float) $obj;

			// Create NumberType for each item
			$converted[$i] = Factory::create('Papier\Type\NumberType', $value);
		}

		/** @var NumbersArrayType $object */
		$object = Factory::create('Papier\Type\NumbersArrayType');
		$object->setValue($converted);

		return $object;
	}

}