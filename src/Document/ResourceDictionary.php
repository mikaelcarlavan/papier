<?php

namespace Papier\Document;

use Papier\Base\IndirectObject;
use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use InvalidArgumentException;

class ResourceDictionary extends IndirectObject
{
    /**
     * Create a new ResourceDictionary instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = new DictionaryObject();
        parent::__construct();
    } 

    /**
     * Get resource's dictionary.
     *
     * @return string
     */
    private function getDictionary()
    {
        return $this->getValue();
    }

    /**
     * Add entry to resource's dictionnary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Document\ResourceDictionary
     */
    private function addEntry($key, $object)
    {
        $this->getDictionary()->setObjectForKey($key, $object);
        return $this;
    } 

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
            throw new InvalidArgumentException("ExtGState is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $state->setIndirect(true);
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
            throw new InvalidArgumentException("Colour is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $colour->setIndirect(true);
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
            throw new InvalidArgumentException("Pattern is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $pattern->setIndirect(true);
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
            throw new InvalidArgumentException("Shading is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $shading->setIndirect(true);
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
            throw new InvalidArgumentException("XObject is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $xobject->setIndirect(true);
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
            throw new InvalidArgumentException("Font is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $font->setIndirect(true);
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
            throw new InvalidArgumentException("ProcSet is incorrect. See ".get_class($this)." class's documentation for possible values.");
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
            throw new InvalidArgumentException("Properties is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Properties', $properties);
        return $this;
    }

    /**
     * Format resource's content.
     *
     * @return string
     */
    public function format()
    {
        $dictionary = $this->getDictionary();
        $value = $dictionary->write();
        
        return $value;
    }
}