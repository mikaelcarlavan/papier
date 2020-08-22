<?php

namespace Papier\Type;

use Papier\Type\ArrayType;
use Papier\Type\BooleanType;

use Papier\Validator\BooleanValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BooleansArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class BooleansArrayType extends ArrayType
{
    /**
    * Set object's numbers.
    *
    * @param  array  $values
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'float' or 'int'.
    * @return \Papier\Type\NumbersArrayType
    */
    public function setValue($booleans)
    {
        if (!BooleansArrayValidator::isValid($booleans)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($booleans as $i => $boolean) {
            $value = Factory::create('Boolean', $boolean);
            $objects[$i] = $value;
        }

        return parent::setValue($objects);
    } 
}