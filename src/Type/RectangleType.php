<?php

namespace Papier\Type;

use Papier\Validator\NumberValidator;
use Papier\Factory\Factory;

use InvalidArgumentException;

class RectangleType extends NumbersArrayType
{
    /**
    * Set object's lower left X coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setLowerLeftX($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $coordinate);
		$this->setObjectForKey(0, $value);

        return $this;
    } 
    
    
    /**
    * Set object's lower left Y coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setLowerLeftY($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $coordinate);
		$this->setObjectForKey(1, $value);

        return $this;
    } 

    /**
    * Set object's upper right X coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setUpperRightX($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $coordinate);
		$this->setObjectForKey(2, $value);
        return $this;
    } 

    /**
    * Set object's upper right Y coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setUpperRightY($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $coordinate);
		$this->setObjectForKey(3, $value);
        return $this;
    }
}