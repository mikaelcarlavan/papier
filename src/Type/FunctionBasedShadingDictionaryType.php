<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;
use Papier\Factory\Factory;

use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\ArrayValidator;

use InvalidArgumentException;
use RuntimeException;

class FunctionBasedShadingDictionaryType extends ShadingDictionaryType
{
    /**
     * Set domain.
     *  
     * @param  array<mixed> $domain
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return FunctionBasedShadingDictionaryType
     */
    public function setDomain(array $domain): FunctionBasedShadingDictionaryType
    {
        if (!ArrayValidator::isValid($domain, 4)) {
            throw new InvalidArgumentException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\ArrayType', $domain);

        $this->setEntry('Domain', $value);
        return $this;
    } 

    /**
     * Set transformation matrix.
     *  
     * @param  array<float>  $matrix
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return FunctionBasedShadingDictionaryType
     */
    public function setMatrix(array $matrix): FunctionBasedShadingDictionaryType
    {
        if (!NumbersArrayValidator::isValid($matrix, 6)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumbersArrayType', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }

    /**
     * Set function.
     *  
     * @param FunctionObject $function
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     * @return FunctionBasedShadingDictionaryType
     */
    public function setFunction(FunctionObject $function): FunctionBasedShadingDictionaryType
    {
        $this->setEntry('Function', $function);
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

        return parent::format();
    }
}
