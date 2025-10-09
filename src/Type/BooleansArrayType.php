<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\ArrayType;
use Papier\Validator\BooleansArrayValidator;

class BooleansArrayType extends ArrayType
{
    /**
     * Set object's numbers.
     *
     * @param mixed $value
     * @return BooleansArrayType
     * @throws InvalidArgumentException if the provided argument is not an array of 'float' or 'int'.
     */
    public function setValue(mixed $value): BooleansArrayType
    {
        if (!BooleansArrayValidator::isValid($value)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		/** @var BooleansArrayType $value */
        $objects = $this->getObjects();

        foreach ($value as $i => $val) {
            $object = Factory::create('Papier\Type\Base\BooleanType', $val);
            $objects[$i] = $object;
        }

		parent::setValue($objects);
        return $this;
    }

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return BooleansArrayType
	 */
	public static function fromString(string $data): BooleansArrayType
	{
		$object = new BooleansArrayType();

		// Parse array using parent parser
		$array = parent::fromString($data);

		$values = $array->all(); // get raw values

		$boolObjects = [];
		foreach ($values as $i => $val) {
			$boolObjects[$i] = Factory::create('Papier\Object\BooleanObject', $val);
		}

		$object->setValue($boolObjects);

		return $object;
	}

}