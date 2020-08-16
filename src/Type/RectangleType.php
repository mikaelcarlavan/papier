<?php

namespace Papier\Type;

use Papier\Type\ArrayType;
use Papier\Type\NumberType;

use Papier\Validator\NumberValidator;
use Papier\Validator\ArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class RectangleType extends ArrayType
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

        $value = Factory::getInstance()->createType('Number', $coordinate, false);

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

        $value = Factory::getInstance()->createType('Number', $coordinate, false);

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

        $value = Factory::getInstance()->createType('Number', $coordinate, false);

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

        $value = Factory::getInstance()->createType('Number', $coordinate, false);

        $objects = $this->getObjects();
        $objects[3] = $value;

        return $this->setObjects($objects);
    } 

    /**
    * Set object's coordinates.
    *
    * @param  array  $coordinates
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'float' or 'int'.
    * @return \Papier\Type\RectangleType
    */
    public function setCoordinates($coordinates)
    {
        if (!ArrayValidator::isValid($coordinates)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($coordinates as $i => $coordinate) {
            if (!NumberValidator::isValid($coordinate)) {
                throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }

            $value = Factory::getInstance()->createType('Number', $coordinate, false);

            $objects[$i] = $value;
        }

        return $this->setObjects($objects);
    } 
}