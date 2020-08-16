<?php

namespace Papier\Type;

use Papier\Type\ArrayType;
use Papier\Object\RealObject;
use Papier\Validator\RealValidator;
use Papier\Validator\ArrayValidator;

use InvalidArgumentException;

class RectangleType extends ArrayType
{
    /**
    * Set object's lower left X coordinate.
    *
    * @param  float  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setLowerLeftX($coordinate)
    {
        if (!RealValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new RealObject();
        $value->setValue($coordinate);

        $objects = $this->getObjects();
        $objects[0] = $value;

        return $this->setObjects($objects);
    } 
    
    
    /**
    * Set object's lower left Y coordinate.
    *
    * @param  float  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setLowerLeftY($coordinate)
    {
        if (!RealValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new RealObject();
        $value->setValue($coordinate);

        $objects = $this->getObjects();
        $objects[1] = $value;

        return $this->setObjects($objects);
    } 

    /**
    * Set object's upper right X coordinate.
    *
    * @param  float  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setUpperRightX($coordinate)
    {
        if (!RealValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new RealObject();
        $value->setValue($coordinate);

        $objects = $this->getObjects();
        $objects[2] = $value;

        return $this->setObjects($objects);
    } 

    /**
    * Set object's upper right Y coordinate.
    *
    * @param  float  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setUpperRightY($coordinate)
    {
        if (!RealValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new RealObject();
        $value->setValue($coordinate);

        $objects = $this->getObjects();
        $objects[3] = $value;

        return $this->setObjects($objects);
    } 

    /**
    * Set object's coordinates.
    *
    * @param  array  $coordinates
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'float.
    * @return \Papier\Type\RectangleType
    */
    public function setCoordinates($coordinates)
    {
        if (!ArrayValidator::isValid($coordinates)) {
            throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($coordinates as $i => $coordinate) {
            if (!RealValidator::isValid($coordinate)) {
                throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }

            $value = new RealObject();
            $value->setValue($coordinate);

            $objects[$i] = $value;
        }

        return $this->setObjects($objects);
    } 
}