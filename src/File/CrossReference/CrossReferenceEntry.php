<?php

namespace Papier\File\CrossReference;

use Papier\Object\IndirectObject;

class CrossReferenceEntry extends IndirectObject
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
     * @return \Papier\File\CrossReference\CrossReferenceEntry
     */
    protected function setOffset($offset)
    {
        if (!IntValidator::isValid($offset, 0)) {
            throw new InvalidArgumentException("Offset is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->offset = $offset;
        return $this;
    } 

    /**
     * Set entry to be free.
     *  
     * @param  bool  $free
     * @return \Papier\File\CrossReference\CrossReferenceEntry
     */
    protected function setFree($free = true)
    {
        if (!BoolValidator::isValid($free)) {
            throw new InvalidArgumentException("Free boolean is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->free = $free;
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
    protected function getOffset()
    {
        return $this->offset;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $keyword = $this->isFree() ? self::FREE_ENTRY : self::IN_USE_ENTRY;
        $value = sprintf("%010d %05d %s", $this->getOffset(), $this->getGeneration(), $keyword);
        return $value;
    }
}