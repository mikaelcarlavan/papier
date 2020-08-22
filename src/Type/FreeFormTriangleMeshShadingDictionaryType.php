<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\FunctionObject;

use Papier\Factory\Factory;

use Papier\Type\ShadingDictionaryType;

use Papier\Validator\ShadingTypeValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\BitsPerCoordinateValidator;
use Papier\Validator\BitsPerComponentValidator;
use Papier\Validator\BitsPerFlagValidator;

use InvalidArgumentException;

class FreeFormTriangleMeshShadingDictionaryType extends ShadingDictionaryType
{
    /**
     * Set the number of bits used to represent each vertex coordinate.
     *  
     * @param  array  $bits
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\FreeFormTriangleMeshShadingDictionaryType
     */
    public function setBitsPerCoordinate($bits)
    {
        if (!BitsPerCoordinateValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerCoordinate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $bits);

        $this->setEntry('BitsPerCoordinate', $value);
        return $this;
    }

     /**
     * Set the number of bits used to represent each colour component.
     *  
     * @param  array  $bits
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\FreeFormTriangleMeshShadingDictionaryType
     */
    public function setBitsPerComponent($bits)
    {
        if (!BitsPerComponentValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerComponent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $bits);

        $this->setEntry('BitsPerComponent', $value);
        return $this;
    }

     /**
     * Set the number of bits used to represent the edge flag of each vertex.
     *  
     * @param  array  $bits
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\FreeFormTriangleMeshShadingDictionaryType
     */
    public function setBitsPerFlag($bits)
    {
        if (!BitsPerFlagValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerFlag is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $bits);

        $this->setEntry('BitsPerFlag', $value);
        return $this;
    }

    /**
     * Set map from vertex coordinates and colour components to the appropriate ranges of values.
     *  
     * @param  array  $decode
     * @throws InvalidArgumentException if the provided argument is not of type 'array' and if each element of the provided argument is not of type 'int' or 'float.
     * @return \Papier\Type\FreeFormTriangleMeshShadingDictionaryType
     */
    public function setDecode($decode)
    {
        if (!NumbersArrayValidator::isValid($decode)) {
            throw new InvalidArgumentException("Decode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $decode);

        $this->setEntry('Decode', $value);
        return $this;
    }

    /**
     * Set function.
     *  
     * @param  \Papier\Object\FunctionObject  $function
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     * @return \Papier\Type\FreeFormTriangleMeshShadingDictionaryType
     */
    public function setFunction($function)
    {
        if (!$function instanceof FunctionObject) {
            throw new InvalidArgumentException("Function is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Function', $function);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        if (!$this->hasEntry('BitsPerCoordinate')) {
            throw new RuntimeException("BitsPerCoordinate is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('BitsPerComponent')) {
            throw new RuntimeException("BitsPerComponent is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('BitsPerFlag')) {
            throw new RuntimeException("BitsPerFlag is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Decode')) {
            throw new RuntimeException("Decode is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}