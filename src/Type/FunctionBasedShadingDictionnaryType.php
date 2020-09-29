<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\FunctionObject;

use Papier\Factory\Factory;

use Papier\Type\ShadingDictionaryType;

use Papier\Validator\ShadingTypeValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\BooleanValidator;

use InvalidArgumentException;

class FunctionBasedShadingDictionaryType extends ShadingDictionaryType
{
    /**
     * Set domain.
     *  
     * @param  array $domain
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\FunctionBasedShadingDictionaryType
     */
    public function setDomain($domain)
    {
        if (!ArrayValidator::isValid($domain, 4)) {
            throw new InvalidArgumentException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Array', $domain);

        $this->setEntry('Domain', $value);
        return $this;
    } 

    /**
     * Set transformation matrix.
     *  
     * @param  array  $matrix
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\FunctionBasedShadingDictionaryType
     */
    public function setMatrix($matrix)
    {
        if (!NumbersArrayValidator::isValid($matrix, 6)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }

    /**
     * Set function.
     *  
     * @param  \Papier\Object\FunctionObject  $function
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     * @return \Papier\Type\FunctionBasedShadingDictionaryType
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
        if (!$this->hasEntry('Function')) {
            throw new RuntimeException("Function is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}
