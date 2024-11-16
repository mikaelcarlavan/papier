<?php

namespace Papier\Type;


use Papier\Object\FunctionObject;

use Papier\Factory\Factory;


use Papier\Validator\BooleansArrayValidator;
use Papier\Validator\NumbersArrayValidator;

use InvalidArgumentException;
use RuntimeException;

class RadialShadingDictionaryType extends ShadingDictionaryType
{
    /**
     * Set centers and radii of the starting and ending circles.
     *  
     * @param array<float> $coords
     * @return RadialShadingDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setCoords(array $coords): RadialShadingDictionaryType
    {
        if (!NumbersArrayValidator::isValid($coords, 6)) {
            throw new InvalidArgumentException("Coords is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumbersArrayType', $coords);

        $this->setEntry('Coords', $value);
        return $this;
    }

    /**
     * Set limiting values.
     *  
     * @param array<float> $domain
     * @return RadialShadingDictionaryType
     *@throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setDomain(array $domain): RadialShadingDictionaryType
    {
        if (!NumbersArrayValidator::isValid($domain, 2)) {
            throw new InvalidArgumentException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumbersArrayType', $domain);

        $this->setEntry('Domain', $value);
        return $this;
    }
  
    /**
     * Set function.
     *  
     * @param FunctionObject $function
     * @return RadialShadingDictionaryType
     */
    public function setFunction(FunctionObject $function): RadialShadingDictionaryType
    {
        $this->setEntry('Function', $function);
        return $this;
    }

    /**
     * Allow extension of the shading beyond the starting and ending circles.
     *  
     * @param  array<bool>  $extend
     * @return RadialShadingDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setExtend(array $extend): RadialShadingDictionaryType
    {
        if (!BooleansArrayValidator::isValid($extend, 2)) {
            throw new InvalidArgumentException("Extend is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\BooleansArrayType', $extend);

        $this->setEntry('Extend', $value);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
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