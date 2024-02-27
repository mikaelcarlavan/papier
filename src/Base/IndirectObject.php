<?php

namespace Papier\Base;

use Papier\Repository\Repository;

use Papier\Validator\IntegerValidator;

use InvalidArgumentException;

abstract class IndirectObject extends BaseObject
{
    /**
     * Define object as indirect.
     *
     * @var bool
     */
    protected bool $indirect = false;

    /**
     * The number of the object.
     *
     * @var int
     */
    protected int $number = 1;
  
    /**
     * The generation number of the object.
     *
     * @var int
     */
    protected int $generation = 0;

    /**
     * Set object's number.
     *
     * @param int $number
     * @return IndirectObject
     */
    public function setNumber(int $number): IndirectObject
    {
        if (!IntegerValidator::isValid($number, 1)) {
            throw new InvalidArgumentException("Object number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->number = $number;
        return $this;
    }

    /**
     * Set object's generation number.
     *
     * @param int $generation
     * @return IndirectObject
     */
    public function setGeneration(int $generation): IndirectObject
    {
        $this->generation = $generation;
        return $this;
    }

    /**
     * Set object to be indirect.
     *
     * @param bool $indirect
     * @return IndirectObject
     */
    public function setIndirect(bool $indirect = true): IndirectObject
    {
        $this->indirect = $indirect;

        if ($indirect) {
            Repository::getInstance()->addObject($this);
        } else {
            Repository::getInstance()->removeObject($this);
        }

        return $this;
    } 

    /**
     * Returns if object is indirect.
     *  
     * @return bool
     */
    public function isIndirect(): bool
    {
        return $this->indirect;
    } 


    /**
     * Get object's number.
     *
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * Get object's generation number.
     *
     * @return int
     */
    public function getGeneration(): int
    {
        return $this->generation;
    }

    /**
     * Get object's reference.
     *
     * @return string
     */
    public function getReference(): string
    {
        return sprintf('%d %d R', $this->getNumber(), $this->getGeneration());
    }


    /**
     * Get object.
     *
     * @return string
     */
    public function getObject(): string
    {
        $value = sprintf('%d %d obj', $this->getNumber(), $this->getGeneration()). self::EOL_MARKER;
        $value .= $this->format(). self::EOL_MARKER;
        $value .= 'endobj'. self::EOL_MARKER;
        
        return $value;
    }

    /**
     * Write object's value.
     *
     * @return string
     */
    public function write(): string
    {
        $value = $this->isIndirect() ? $this->getReference() : $this->format();
        return $value. self::EOL_MARKER;
    }
}