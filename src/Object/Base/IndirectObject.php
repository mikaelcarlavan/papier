<?php

namespace Papier\Object\Base;

use Papier\Base\Object;
use Papier\Validator\BoolValidator;

abstract class IndirectObject extends Object
{
    /**
     * Define object as indirect.
     *
     * @var bool
     */
    protected $isIndirect = false;

    /**
     * The number of the object.
     *
     * @var int
     */
    protected $number = 1;
  
    /**
     * The generation number of the object.
     *
     * @var int
     */
    protected $generation = 0;

    /**
     * Set object's number.
     *  
     * @param  int  $number
     * @return \Papier\Object\Base\IndirectObject
     */
    protected function setNumber($number)
    {
        if (!IntValidator::isValid($number, 1)) {
            throw new InvalidArgumentException("Object number is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->number = $number;
        return $this;
    } 

    /**
     * Set object's generation number.
     *  
     * @param  int  $generation
     * @return \Papier\Object\Base\IndirectObject
     */
    protected function setGeneration($generation)
    {
        if (!IntValidator::isValid($generation)) {
            throw new InvalidArgumentException("Generation number is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->generation = $generation;
        return $this;
    } 

    /**
     * Set object to be indirect.
     *  
     * @param  bool  $indirect
     * @return \Papier\Object\Base\IndirectObject
     */
    protected function setIndirect($indirect = true)
    {
        if (!BoolValidator::isValid($indirect)) {
            throw new InvalidArgumentException("Indirect boolean is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->indirect = $indirect;
        return $this;
    } 

    /**
     * Returns if object is indirect.
     *  
     * @return bool
     */
    protected function isIndirect()
    {
        return $this->indirect;
    } 


    /**
     * Get object's number.
     *
     * @return int
     */
    protected function getNumber()
    {
        return $this->number;
    }

    /**
     * Get object's generation number.
     *
     * @return int
     */
    protected function getGeneration()
    {
        return $this->generation;
    }

    /**
     * Get object's reference.
     *
     * @return string
     */
    protected function getReference()
    {
        $reference = sprintf('%d %d R', $this->getNumber(), $this->getGeneration());
        return $reference;
    }

    /**
     * Write object's value.
     *
     * @return string
     */
    public function write()
    {
        $value = null;
        if ($this->isIndirect()) {
            $value = sprintf('%d %d obj', $this->getNumber(), $this->getGeneration());
            $value .= $this->getReference(). $this->EOL_MARKER;
            $value .= 'endobj';
        } else {
            $value = $this->format(). $this->EOL_MARKER;
        }

        return $value;
    }
}