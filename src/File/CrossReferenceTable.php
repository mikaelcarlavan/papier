<?php

namespace Papier\File;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSection;

class CrossReferenceTable extends ArrayObject
{
    /**
     * Add new section.
     *  
     * @return \Papier\File\CrossReference\CrossReferenceSection
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
        $objects = $this->getSections();

        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}