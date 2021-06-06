<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use InvalidArgumentException;

class ResourceDictionaryType extends DictionaryObject
{
    /**
     * Set graphics state parameter mapping dictionary.
     *  
     * @param  DictionaryObject$state
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return ResourceDictionaryType
     */
    public function setExtGState(DictionaryObject $state): ResourceDictionaryType
    {
        $this->setEntry('ExtGState', $state);
        return $this;
    } 


    /**
     * Set colour space mapping dictionary.
     *  
     * @param  DictionaryObject  $colour
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return ResourceDictionaryType
     */
    public function setColourSpace(DictionaryObject $colour): ResourceDictionaryType
    {
        $this->setEntry('ColourSpace', $colour);
        return $this;
    }
    
    /**
     * Set pattern mapping dictionary.
     *  
     * @param  DictionaryObject  $pattern
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return ResourceDictionaryType
     */
    public function setPattern(DictionaryObject $pattern): ResourceDictionaryType
    {
        $this->setEntry('Pattern', $pattern);
        return $this;
    } 

    /**
     * Set shading mapping dictionary.
     *  
     * @param  DictionaryObject  $shading
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return ResourceDictionaryType
     */
    public function setShading(DictionaryObject $shading): ResourceDictionaryType
    {
        $this->setEntry('Shading', $shading);
        return $this;
    } 

    /**
     * Set external objects mapping dictionary.
     *  
     * @param  DictionaryObject  $xobject
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return ResourceDictionaryType
     */
    public function setXObject(DictionaryObject $xobject): ResourceDictionaryType
    {
        $this->setEntry('XObject', $xobject);
        return $this;
    } 

    /**
     * Set font mapping dictionary.
     *  
     * @param  DictionaryObject  $font
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return ResourceDictionaryType
     */
    public function setFont(DictionaryObject $font): ResourceDictionaryType
    {
        $this->setEntry('Font', $font);
        return $this;
    } 

    /**
     * Set procedure set names.
     *  
     * @param ArrayObject $procset
     * @return ResourceDictionaryType
     */
    public function setProcSet(ArrayObject $procset): ResourceDictionaryType
    {
        $this->setEntry('ProcSet', $procset);
        return $this;
    } 

    /**
     * Set property list mapping dictionary.
     *  
     * @param DictionaryObject $properties
     * @return ResourceDictionaryType
     */
    public function setProperties(DictionaryObject $properties): ResourceDictionaryType
    {
        $this->setEntry('Properties', $properties);
        return $this;
    }
}