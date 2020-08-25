<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\StringValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;

use InvalidArgumentException;

class LabColourSpaceDictionaryType extends DictionaryObject
{
    /**
     * Set white point.
     * 
     * @param array $whitepoint
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     * @return \Papier\Type\LabColourSpaceDictionaryType
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
     * @return \Papier\Type\LabColourSpaceDictionaryType
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
     * Set range.
     * 
     * @param array $range
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     * @return \Papier\Type\LabColourSpaceDictionaryType
     */
    public function setRange($range)
    {
        if (!NumbersArrayValidator::isValid($range, 4)) {
            throw new InvalidArgumentException("Range is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $range);

        $this->setEntry('Range', $value);
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