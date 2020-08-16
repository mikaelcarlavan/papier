<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;
use Papier\Object\IntegerObject;
use Papier\Object\ArrayObject;

use Papier\Functions\FunctionType;

use Papier\Validator\BitsPerSampleValidator;
use Papier\Validator\IntValidator;

use InvalidArgumentException;
use RuntimeException;

class StitchingFunctionType extends FunctionObject
{
    
    /**
     * Set functions.
     *  
     * @param  \Papier\Object\ArrayObject  $functions
     * @throws InvalidArgumentException if the provided argument is not a valid function type.
     * @return \Papier\Type\StitchingFunctionType
     */
    public function setFunctions($functions)
    {
        if (!$functions instanceof ArrayObject) {
            throw new InvalidArgumentException("Functions is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Functions', $functions);
        return $this;
    } 

    /**
     * Set encode.
     *  
     * @param  int  $encode
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\StitchingFunctionType
     */
    public function setEncode($encode)
    {
        if (!$encode instanceof ArrayObject) {
            throw new InvalidArgumentException("Encode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Encode', $encode);
        return $this;
    } 

    /**
     * Set bounds.
     *  
     * @param  \Papier\Object\ArrayObject  $bounds
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\StitchingFunctionType
     */
    public function setBounds($bounds)
    {
        if (!$bounds instanceof ArrayObject) {
            throw new InvalidArgumentException("Decode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Bounds', $bounds);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
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

        return parent::format();
    }
}