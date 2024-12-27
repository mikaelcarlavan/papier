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
}