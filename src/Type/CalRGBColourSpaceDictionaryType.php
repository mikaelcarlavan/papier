<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\StringValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;

use InvalidArgumentException;

class CalRGBColourSpaceDictionaryType extends DictionaryObject
{
    /**
     * Set white point.
     * 
     * @param array $whitepoint
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     * @return \Papier\Type\CalRGBColourSpaceDictionaryType
     */
    public function setWhitePoint($whitepoint)
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
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     * @return \Papier\Type\CalRGBColourSpaceDictionaryType
     */
    public function setBlackPoint($blackpoint)
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
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or 'float'.
     * @return \Papier\Type\CalRGBColourSpaceDictionaryType
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
     * Set interpolation matrix.
     * 
     * @param array $matrix
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     * @return \Papier\Type\CalRGBColourSpaceDictionaryType
     */
    public function setMatrix($matrix)
    {
        if (!NumbersArrayValidator::isValid($matrix, 9)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        if (!$this->hasEntry('WhitePoint')) {
            throw new RuntimeException("WhitePoint is missing. See ".__CLASS__." class's documentation for possible values.");
        }
                
        return parent::format();
    }
}