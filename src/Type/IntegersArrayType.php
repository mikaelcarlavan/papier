<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\ArrayType;
use Papier\Validator\IntegersArrayValidator;

class IntegersArrayType extends ArrayType
{
    /**
    * Set object's numbers.
    *
    * @param mixed $value
    * @return IntegersArrayType
    *@throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'int'.
    */
    public function setValue(mixed $value): IntegersArrayType
    {
        if (!IntegersArrayValidator::isValid($value)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		/** @var IntegersArrayType $value */
        $objects = $this->getObjects();

        foreach ($value as $i => $val) {
            $object = Factory::create('Papier\Type\Base\IntegerType', $val);
            $objects[$i] = $object;
        }

		parent::setValue($objects);

        return $this;
    } 
}