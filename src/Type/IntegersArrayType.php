<?php

namespace Papier\Type;

use Papier\Type\ArrayType;

use Papier\Validator\IntegersArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class IntegersArrayType extends ArrayType
{
    /**
    * Set object's numbers.
    *
    * @param  array  $values
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'int'.
    * @return \Papier\Type\IntegersArrayType
    */
    public function setValue($numbers)
    {
        if (!IntegersArrayValidator::isValid($numbers)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($numbers as $i => $number) {
            $value = Factory::create('Integer', $number);
            $objects[$i] = $value;
        }

        return parent::setValue($objects);
    } 
}