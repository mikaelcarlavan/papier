<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceEntry;

class CrossReferenceSubsection extends ArrayObject
{
    /**
     * Magical method.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @throws InvalidArgumentException if the provided argument does not inherit 'CrossReferenceEntry'.
     * @return \Papier\File\CrossReference\CrossReferenceSubsection
     */
    public function __set($key, $object)
    {
        if (!$object instanceof CrossReferenceEntry) {
            throw new InvalidArgumentException("Cross-reference entry is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }
        $this->setObjectForKey($key, $object);
    }

    /**
     * Magical method.
     *  
     * @param  string  $key
     * @return \Papier\File\CrossReference\CrossReferenceEntry
     */
    public function __get($key)
    {
        $this->getObjectForKey($key);
    }

    /**
     * Add new subsection.
     *  
     * @return \Papier\File\CrossReference\CrossReferenceEntry
     */
    public function addEntry()
    {
        $objects = $this->getEntries();

        $object = new CrossReferenceEntry();
        $objects[] = $object;

        $this->setObjects($objects);

        return $object;
    }

    /**
     * Get subsections.
     *  
     * @return array
     */
    public function getEntries()
    {
        $objects = $this->getObjects();
        return $objects;
    }  

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getEntries();
        $firstNumber = 0;
        if (is_array($objects) && count($objects) > 0) {
            $firstNumber = $objects[0]->getNumber();
        }
        $value = sprintf("%d %d", $firstNumber, count($objects));
        return $value;
    }
}