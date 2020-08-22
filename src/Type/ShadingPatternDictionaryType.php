<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;

use Papier\Type\PatternDictionaryType;

use Papier\Factory\Factory;

use Papier\Graphics\PatternType;


use InvalidArgumentException;
use RuntimeException;

class ShadingPatternDictionaryType extends PatternDictionaryType
{
    /**
     * Set shading.
     *  
     * @param  mixed  $shading
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'StreamObject'.
     * @return \Papier\Type\ShadingPatternDictionaryType
     */
    public function setShading($shading)
    {
        if (!$shading instanceof DictionaryObject && !$shading instanceof StreamObject) {
            throw new InvalidArgumentException("Shading is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Shading', $shading);
        return $this;
    } 
  

    /**
     * Set pattern matrix.
     *  
     * @param  array  $matrix
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\ShadingPatternDictionaryType
     */
    public function setMatrix($matrix)
    {
        if (!NumbersArrayValidator::isValid($matrix, 6)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Array', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }
    
    /**
     * Set graphics state parameter dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $extgstate
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\ShadingPatternDictionaryType
     */
    public function setExtGState($extgstate)
    {
        if (!$extgstate instanceof DictionaryObject) {
            throw new InvalidArgumentException("ExtGState is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ExtGState', $extgstate);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $this->setPatternType(PatternType::SHADING_PATTERN);

        if (!$this->hasEntry('Shading')) {
            throw new RuntimeException("Shading is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}