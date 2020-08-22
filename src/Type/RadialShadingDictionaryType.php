<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\FunctionObject;

use Papier\Factory\Factory;

use Papier\Type\ShadingDictionaryType;

use Papier\Validator\ShadingTypeValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\BooleansArrayValidator;
use Papier\Validator\NumbersArrayValidator;

use InvalidArgumentException;

class RadialShadingDictionaryType extends ShadingDictionaryType
{
    /**
     * Set centers and radii of the starting and ending circlees.
     *  
     * @param  array  $coords
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\RadialShadingDictionaryType
     */
    public function setCoords($coords)
    {
        if (!NumbersArrayValidator::isValid($coords, 6)) {
            throw new InvalidArgumentException("Coords is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $coords);

        $this->setEntry('Coords', $value);
        return $this;
    }

    /**
     * Set limiting values.
     *  
     * @param  array  $domain
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\RadialShadingDictionaryType
     */
    public function setDomain($domain)
    {
        if (!NumbersArrayValidator::isValid($domain, 2)) {
            throw new InvalidArgumentException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $domain);

        $this->setEntry('Domain', $value);
        return $this;
    }
  
    /**
     * Set function.
     *  
     * @param  \Papier\Object\FunctionObject  $function
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     * @return \Papier\Type\RadialShadingDictionaryType
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
     * Allow extension of the shading beyond the starting and ending circles.
     *  
     * @param  array  $domain
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\RadialShadingDictionaryType
     */
    public function setExtend($extend)
    {
        if (!BooleansArrayValidator::isValid($extend, 2)) {
            throw new InvalidArgumentException("Extend is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('BooleansArray', $extend);

        $this->setEntry('Extend', $value);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        if (!$this->hasEntry('Function')) {
            throw new RuntimeException("Function is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Coords')) {
            throw new RuntimeException("Coords is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}