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
     * @param mixed $booleans
     * @return BooleansArrayType
     * @throws InvalidArgumentException if the provided argument is not an array of 'float' or 'int'.
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