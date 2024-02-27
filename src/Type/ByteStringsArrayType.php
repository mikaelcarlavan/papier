<?php

namespace Papier\Type;

use Papier\Validator\ByteStringsArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class ByteStringsArrayType extends ArrayType
{
    /**
    * Set object's byte strings.
    *
    * @param mixed $value
    * @return ByteStringsArrayType
    *@throws InvalidArgumentException if the provided argument is not an array of 'string'.
    */
    public function setValue(mixed $value): ByteStringsArrayType
    {
        if (!ByteStringsArrayValidator::isValid($value)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($value as $i => $val) {
            $object = Factory::create('ByteString', $val);
            $objects[$i] = $object;
        }

        return parent::setValue($objects);
    } 
}