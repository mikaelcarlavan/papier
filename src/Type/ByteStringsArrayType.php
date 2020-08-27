<?php

namespace Papier\Type;

use Papier\Type\ArrayType;
use Papier\Validator\ByteStringsArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class ByteStringsArrayType extends ArrayType
{
    /**
    * Set object's byte strings.
    *
    * @param  array  $values
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'string'.
    * @return \Papier\Type\ByteStringsArrayType
    */
    public function setValue($strings)
    {
        if (!ByteStringsArrayValidator::isValid($strings)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($strings as $i => $string) {
            $value = Factory::create('ByteString', $string);
            $objects[$i] = $value;
        }

        return parent::setValue($objects);
    } 
}