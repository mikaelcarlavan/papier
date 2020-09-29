<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;

class CrossReferenceSubsection extends ArrayObject
{
    /**
     * Add new subsection.
     *  
     * @return CrossReferenceEntry
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
        return $this->getObjects();
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
            $firstNumber = $objects[0]->getOffset();
        }
        $value = sprintf("%d %d", $firstNumber, count($objects)) . self::EOL_MARKER;

        if (is_array($objects) && count($objects) > 0) {
            foreach ($objects as $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}