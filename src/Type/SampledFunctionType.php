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

class SampledFunctionType extends FunctionObject
{
    
    /**
     * Set size.
     *  
     * @param  \Papier\Object\ArrayObject  $size
     * @throws InvalidArgumentException if the provided argument is not a valid function type.
     * @return \Papier\Type\SampledFunctionType
     */
    public function setSize($size)
    {
        if (!$size instanceof ArrayObject) {
            throw new InvalidArgumentException("Size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Size', $size);
        return $this;
    } 

    /**
     * Set bits per sample.
     *  
     * @param  int  $bits
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\SampledFunctionType
     */
    public function setBitsPerSample($bits)
    {
        if (!BitsPerSampleValidator::isValid($bis)) {
            throw new InvalidArgumentException("BitsPerSample is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new IntegerObject();
        $value->setValue($bits);

        $this->setEntry('BitsPerSample', $value);
        return $this;
    } 

    /**
     * Set order.
     *  
     * @param  int  $order
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\SampledFunctionType
     */
    public function setOrder($order)
    {
        if (!IntValidator::isValid($order, 1, 3)) {
            throw new InvalidArgumentException("Order is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new IntegerObject();
        $value->setValue($order);

        $this->setEntry('Order', $value);
        return $this;
    } 


    /**
     * Set encode.
     *  
     * @param  int  $encode
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\SampledFunctionType
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
     * Set decode.
     *  
     * @param  int  $encode
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\SampledFunctionType
     */
    public function setDecode($decode)
    {
        if (!$decode instanceof ArrayObject) {
            throw new InvalidArgumentException("Decode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Decode', $decode);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $this->setFunctionType(FunctionType::SAMPLED);

        if (!$this->hasEntry('BitsPerSample')) {
            throw new RuntimeException("BitsPerSample is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Encode') && count($this->getEntry('Encode')) != count($this->getEntry('Domain')) ) {
            throw new RuntimeException("Encode size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Decode') && count($this->getEntry('Decode')) != count($this->getEntry('Range')) ) {
            throw new RuntimeException("Decode size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}