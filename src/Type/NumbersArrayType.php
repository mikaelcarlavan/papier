<?php

namespace Papier\Type;

use Papier\Type\ArrayType;
use Papier\Type\NumberType;

use Papier\Validator\NumberValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\NumbersArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class NumbersArrayType extends ArrayType
{
    /**
    * Set object's numbers.
    *
    * @param  array  $values
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'float' or 'int'.
    * @return \Papier\Type\NumbersArrayType
    */
    public function setValue($numbers)
    {
        if (!NumbersArrayValidator::isValid($numbers)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($numbers as $i => $number) {
            $value = Factory::create('Number', $number);
            $objects[$i] = $value;
        }

        return parent::setValue($objects);
    } 
}