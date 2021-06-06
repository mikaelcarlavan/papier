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
    public function setValue($value): BooleansArrayType
    {
        if (!BooleansArrayValidator::isValid($value)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($value as $i => $val) {
            $object = Factory::create('Boolean', $val);
            $objects[$i] = $object;
        }

        return parent::setValue($objects);
    }
}