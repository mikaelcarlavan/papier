<?php

namespace Papier\File;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSection;

class CrossReferenceTable extends ArrayObject
{
    /**
     * Add new section.
     *  
     * @return CrossReferenceSection
     */
    public function addSection()
    {
        $objects = $this->getSections();

        $object = new CrossReferenceSection();
        $objects[] = $object;

        $this->setObjects($objects);

        return $object;
    }

    /**
     * Get subsections.
     *  
     * @return array
     */
    public function getSections()
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
        $objects = $this->getSections();

        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $value .= $object->write();
            }
        }

        return rtrim($value);
    }
}