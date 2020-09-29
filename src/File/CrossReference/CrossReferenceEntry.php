<?php

namespace Papier\File\CrossReference;

use Papier\Base\BaseObject;

use Papier\Validator\IntegerValidator;
use Papier\Validator\BooleanValidator;

use InvalidArgumentException;

class CrossReferenceEntry extends BaseObject
{
     /**
     * Define entry as free.
     *
     * @var bool
     */
    protected $free = false;

    /**
     * The offset in the decoded stream.
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * The generation number of the entry.
     *
     * @var int
     */
    protected $generation = 0;

    /**
     * Keyword for free entry.
     *
     * @var string
     */
    const FREE_ENTRY = "f";

    /**
     * Keyword for in-use entry.
     *
     * @var string
     */
    const IN_USE_ENTRY = "n";

    /**
     * Set entry's offset.
     *  
     * @param  int  $offset
     * @return CrossReferenceEntry
     * @throws InvalidArgumentException if the provided argument does not inherit 'int'.
     */
    public function setOffset(int $offset)
    {
        if (!IntegerValidator::isValid($offset, 0)) {
            throw new InvalidArgumentException("Offset is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->offset = $offset;
        return $this;
    } 

    /**
     * Set entry to be free.
     *  
     * @param  bool  $free
     * @return CrossReferenceEntry
     * @throws InvalidArgumentException if the provided argument does not inherit 'bool'.
     */
    public function setFree($free = true)
    {
        if (!BooleanValidator::isValid($free)) {
            throw new InvalidArgumentException("Free boolean is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->free = $free;
        return $this;
    } 

    /**
     * Set entry's generation number.
     *  
     * @param  int  $generation
     * @return CrossReferenceEntry
     */
    public function setGeneration(int $generation)
    {
        if (!IntegerValidator::isValid($generation)) {
            throw new InvalidArgumentException("Generation number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->generation = $generation;
        return $this;
    } 

    /**
     * Returns if entry is free.
     *  
     * @return bool
     */
    protected function isFree()
    {
        return $this->free;
    } 


    /**
     * Get entry's offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get entry's generation number.
     *
     * @return int
     */
    public function getGeneration()
    {
        return $this->generation;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $keyword = $this->isFree() ? self::FREE_ENTRY : self::IN_USE_ENTRY;
        return sprintf("%010d %05d %s", $this->getOffset(), $this->getGeneration(), $keyword);
    }
}