<?php

namespace Papier\Base;

use Papier\Base\BaseObject;
use Papier\Validator\BoolValidator;

abstract class IndirectObject extends BaseObject
{
    /**
     * Define object as indirect.
     *
     * @var bool
     */
    protected $indirect = false;

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
        $value = sprintf('%d %d R', $this->getNumber(), $this->getGeneration());
        return $value;
    }


    /**
     * Get object's content.
     *
     * @return string
     */
    protected function getContent()
    {
        $value = sprintf('%d %d obj', $this->getNumber(), $this->getGeneration()). self::EOL_MARKER;
        $value .= $this->format(). self::EOL_MARKER;
        $value .= 'endobj';
        
        return $value;
    }

    /**
     * Write object's value.
     *
     * @return string
     */
    public function write()
    {
        $value = $this->isIndirect() ? $this->getReference() : $this->getContent();
        return $value. self::EOL_MARKER;
    }
}