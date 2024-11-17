<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;

use Papier\Type\PatternDictionaryType;

use Papier\Factory\Factory;

use Papier\Graphics\PatternType;


use InvalidArgumentException;
use Papier\Validator\NumbersArrayValidator;
use RuntimeException;

class ShadingPatternDictionaryType extends PatternDictionaryType
{
    /**
     * Set shading.
     *  
     * @param  DictionaryObject  $shading
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'StreamObject'.
     * @return ShadingPatternDictionaryType
     */
    public function setShading(DictionaryObject $shading): ShadingPatternDictionaryType
    {
        $this->setEntry('Shading', $shading);
        return $this;
    } 
  

    /**
     * Set pattern matrix.
     *  
     * @param array<float> $matrix
     * @return ShadingPatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setMatrix(array $matrix): ShadingPatternDictionaryType
    {
        if (!NumbersArrayValidator::isValid($matrix, 6)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\ArrayType', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }
    
    /**
     * Set graphics state parameter dictionary.
     *  
     * @param DictionaryObject $extgstate
     * @return ShadingPatternDictionaryType
     */
    public function setExtGState(DictionaryObject $extgstate): ShadingPatternDictionaryType
    {
        $this->setEntry('ExtGState', $extgstate);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setPatternType(PatternType::SHADING_PATTERN);

        if (!$this->hasEntry('Shading')) {
            throw new RuntimeException("Shading is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}