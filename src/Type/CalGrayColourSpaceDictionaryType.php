<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;

use InvalidArgumentException;

class CalGrayColourSpaceDictionaryType extends DictionaryObject
{
    /**
     * Set white point.
     * 
     * @param array $whitepoint
     * @return CalGrayColourSpaceDictionaryType
     * @throws InvalidArgumentException if the provided argument is not an 3 length array of type 'int' or 'float'.
     */
    public function setWhitePoint(array $whitepoint)
    {
        if (!NumbersArrayValidator::isValid($whitepoint, 3)) {
            throw new InvalidArgumentException("WhitePoint is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $whitepoint);

        $this->setEntry('WhitePoint', $value);
        return $this;
    } 

    /**
     * Set black point.
     * 
     * @param array $blackpoint
     * @return CalGrayColourSpaceDictionaryType
     * @throws InvalidArgumentException if the provided argument is not an 3 length array of type 'int' or 'float'.
     */
    public function setBlackPoint(array $blackpoint)
    {
        if (!NumbersArrayValidator::isValid($blackpoint, 3)) {
            throw new InvalidArgumentException("BlackPoint is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $blackpoint);

        $this->setEntry('BlackPoint', $value);
        return $this;
    }

    /**
     * Set gamma.
     * 
     * @param mixed $gamma
     * @return CalGrayColourSpaceDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or 'float'.
     */
    public function setGamma($gamma)
    {
        if (!NumberValidator::isValid($gamma)) {
            throw new InvalidArgumentException("Gamma is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $gamma);

        $this->setEntry('Gamma', $value);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     * @throws RuntimeException if white-point is not set.
     */
    public function format()
    {
        if (!$this->hasEntry('WhitePoint')) {
            throw new RuntimeException("WhitePoint is missing. See ".__CLASS__." class's documentation for possible values.");
        }
                
        return parent::format();
    }
}