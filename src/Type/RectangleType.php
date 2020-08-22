<?php

namespace Papier\Type;

use Papier\Type\NumbersArrayType;
use Papier\Type\NumberType;

use Papier\Validator\NumberValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\NumbersArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class RectangleType extends NumbersArrayType
{
    /**
    * Set object's lower left X coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return \Papier\Type\RectangleType
    */
    public function setLowerLeftX($coordinate)
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $coordinate);

        $objects = $this->getObjects();
        $objects[0] = $value;

        return $this->setObjects($objects);
    } 
    
    
    /**
    * Set object's lower left Y coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return \Papier\Type\RectangleType
    */
    public function setLowerLeftY($coordinate)
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $coordinate);

        $objects = $this->getObjects();
        $objects[1] = $value;

        return $this->setObjects($objects);
    } 

    /**
    * Set object's upper right X coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return \Papier\Type\RectangleType
    */
    public function setUpperRightX($coordinate)
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $coordinate);

        $objects = $this->getObjects();
        $objects[2] = $value;

        return $this->setObjects($objects);
    } 

    /**
    * Set object's upper right Y coordinate.
    *
    * @param  mixed  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return \Papier\Type\RectangleType
    */
    public function setUpperRightY($coordinate)
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $coordinate);

        $objects = $this->getObjects();
        $objects[3] = $value;

        return $this->setObjects($objects);
    }
}