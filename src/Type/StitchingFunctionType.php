<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;
use Papier\Object\ArrayObject;

use Papier\Functions\FunctionType;

use RuntimeException;

class StitchingFunctionType extends FunctionObject
{
    
    /**
     * Set functions.
     *  
     * @param ArrayObject $functions
     * @return StitchingFunctionType
     */
    public function setFunctions(ArrayObject $functions): StitchingFunctionType
    {
        $this->setEntry('Functions', $functions);
        return $this;
    } 

    /**
     * Set encode.
     *  
     * @param  ArrayObject  $encode
     * @return StitchingFunctionType
     */
    public function setEncode(ArrayObject $encode): StitchingFunctionType
    {
        $this->setEntry('Encode', $encode);
        return $this;
    } 

    /**
     * Set bounds.
     *  
     * @param  ArrayObject  $bounds
     * @return StitchingFunctionType
     */
    public function setBounds(ArrayObject $bounds): StitchingFunctionType
    {
        $this->setEntry('Bounds', $bounds);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setFunctionType(FunctionType::STITCHING);

        if (!$this->hasEntry('Functions')) {
            throw new RuntimeException("Functions is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Encode')) {
            throw new RuntimeException("Encode is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Bounds')) {
            throw new RuntimeException("Bounds is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $k = count($this->getEntry('Functions'));

        if (count($this->getEntry('Bounds')) != ($k - 1)) {
            throw new RuntimeException("Bounds size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (count($this->getEntry('Encode')) != (2 * $k)) {
            throw new RuntimeException("Encode size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (count($this->getEntry('Domain')) != 2) {
            throw new RuntimeException("Domain size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $domains = $this->getEntry('Domain')->getValue();

        if ($domains->first() > min($this->getEntry('Bounds')->getValue()) || $domains->last() < max($this->getEntry('Bounds')->getValue())) {
            throw new RuntimeException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}