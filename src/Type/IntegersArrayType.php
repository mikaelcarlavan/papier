<?php

namespace Papier\Type;

use Papier\Validator\IntegersArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

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

        $objects = $this->getObjects();

        foreach ($value as $i => $val) {
            $object = Factory::create('Papier\Type\IntegerType', $val);
            $objects[$i] = $object;
        }

		parent::setValue($objects);

        return $this;
    } 
}