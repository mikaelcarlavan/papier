<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceEntry;

class CrossReferenceSubsection extends ArrayObject
{
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