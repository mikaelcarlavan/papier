<?php

namespace Papier\Type;

use Papier\Factory\Factory;

use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;

use Papier\Type\CalGrayColourSpaceDictionaryType;

use RuntimeException;
use InvalidArgumentException;

class CalRGBColourSpaceDictionaryType extends CalGrayColourSpaceDictionaryType
{
    /**
     * Set interpolation matrix.
     * 
     * @param array $matrix
     * @return CalRGBColourSpaceDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a 9 length array of type 'int' or 'float'.
     */
    public function setMatrix(array $matrix)
    {
        if (!NumbersArrayValidator::isValid($matrix, 9)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }
}