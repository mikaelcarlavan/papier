<?php

namespace Papier\Document;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use InvalidArgumentException;

class ResourceDictionary extends DictionaryObject
{
    /**
     * Set graphics state parameter mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $state
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setExtGState($state)
    {
        if (!$state instanceof DictionaryObject) {
            throw new InvalidArgumentException("ExtGState is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('ExtGState', $state);
        return $this;
    } 


    /**
     * Set colour space mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $colour
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setColourSpace($colour)
    {
        if (!$colour instanceof DictionaryObject) {
            throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('ColourSpace', $colour);
        return $this;
    }
    
    /**
     * Set pattern mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $pattern
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setPattern($pattern)
    {
        if (!$pattern instanceof DictionaryObject) {
            throw new InvalidArgumentException("Pattern is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Pattern', $pattern);
        return $this;
    } 

    /**
     * Set shading mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $shading
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setShading($shading)
    {
        if (!$shading instanceof DictionaryObject) {
            throw new InvalidArgumentException("Shading is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Shading', $shading);
        return $this;
    } 

    /**
     * Set external objects mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $xobject
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setXObject($xobject)
    {
        if (!$xobject instanceof DictionaryObject) {
            throw new InvalidArgumentException("XObject is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('XObject', $xobject);
        return $this;
    } 

    /**
     * Set font mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $font
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setFont($font)
    {
        if (!$font instanceof DictionaryObject) {
            throw new InvalidArgumentException("Font is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Font', $font);
        return $this;
    } 

    /**
     * Set procedure set names.
     *  
     * @param  \Papier\Object\ArrayObject  $procset
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setProcSet($procset)
    {
        if (!$procset instanceof ArrayObject) {
            throw new InvalidArgumentException("ProcSet is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('ProcSet', $procset);
        return $this;
    } 

    /**
     * Set property list mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $properties
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setProperties($properties)
    {
        if (!$properties instanceof DictionaryObject) {
            throw new InvalidArgumentException("Properties is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Properties', $properties);
        return $this;
    }
}