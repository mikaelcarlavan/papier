<?php

namespace Papier\Type;

use Papier\Type\ArrayType;
use Papier\Object\RealObject;
use Papier\Validator\RealValidator;

class RectangleType extends ArrayType
{
    /**
     * Create a new RectangleType instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->value[] = ObjectFactory::real(0);
        $this->value[] = ObjectFactory::real(0);
        $this->value[] = ObjectFactory::real(0);
        $this->value[] = ObjectFactory::real(0);
    } 

    /**
    * Set object's lower left X coordinate.
    *
    * @param  float  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setLowerLeftX($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $objects = $this->getObjects();
        $objects[0] = ObjectFactory::real($value);

        return $this->setObjects($objects);
    } 
    
    
    /**
    * Set object's lower left Y coordinate.
    *
    * @param  float  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setLowerLeftY($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $objects = $this->getObjects();
        $objects[1] = ObjectFactory::real($value);

        return $this->setObjects($objects);
    } 

    /**
    * Set object's upper right X coordinate.
    *
    * @param  float  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setUpperRightX($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $objects = $this->getObjects();
        $objects[2] = ObjectFactory::real($value);

        return $this->setObjects($objects);
    } 

    /**
    * Set object's upper right Y coordinate.
    *
    * @param  float  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Type\RectangleType
    */
    public function setUpperRightY($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $objects = $this->getObjects();
        $objects[3] = ObjectFactory::real($value);

        return $this->setObjects($objects);
    } 

    /**
    * Set object's coordinates.
    *
    * @param  array  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'float.
    * @return \Papier\Type\RectangleType
    */
    public function setCoordinates($value)
    {
        if (!ArrayValidator::isValid($value)) {
            throw new InvalidArgumentException("Array is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        foreach ($value as $i => $val) {
            if (!RealValidator::isValid($val)) {
                throw new InvalidArgumentException("Real is incorrect. See ".get_class($this)." class's documentation for possible values.");
            }

            $objects[$i] = ObjectFactory::real($val);
        }

        return $this->setObjects($objects);
    } 
}