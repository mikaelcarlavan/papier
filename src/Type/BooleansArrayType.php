<?php

namespace Papier\Type;

use Papier\Validator\BooleansArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

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
            $object = Factory::create('Papier\Type\BooleanType', $val);
            $objects[$i] = $object;
        }

		parent::setValue($objects);
        return $this;
    }
}