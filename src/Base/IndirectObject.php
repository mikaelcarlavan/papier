<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Base\IntegerObject;
use Papier\Base\DictionaryObject;
use Exception;

class IndirectObject extends Object
{
    /**
     * The number of the object.
     *
     * @var int
     */
    protected $number;
  
    /**
     * The generation number of the object.
     *
     * @var int
     */
    protected $generation;

    /**
     * Create a new IndirectObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->number = 1;
        $this->generation = 0;

        parent::__construct();
    } 


    /**
     * Set object's number.
     *  
     * @param  int  $number
     * @return \Papier\IndirectObject
     */
    protected function setNumber($number)
    {
        if (!IntValidator::isValid($number, 1)) {
            throw new InvalidArgumentException("Object number is incorrect. See IndirectObject class's documentation for possible values.");
        }

        $this->generation = $generation;
        return $this;
    } 

    /**
     * Set object's generation number.
     *  
     * @param  int  $generation
     * @return \Papier\IndirectObject
     */
    protected function setGeneration($generation)
    {
        if (!IntValidator::isValid($generation)) {
            throw new InvalidArgumentException("Generation number is incorrect. See IndirectObject class's documentation for possible values.");
        }

        $this->generation = $generation;
        return $this;
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
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $object = $this->getValue();

        $value = sprintf('%d %d obj', $this->getNumber(), $this->getGeneration());
        if ($object) {
            $value .= $object->format() . $this->EOL_MARKER;
        }
        $value .= 'endobj';

        return $value;
    }
}