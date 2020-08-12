<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSubsection;

class CrossReferenceSection extends ArrayObject
{
    /**
     * Add new subsection.
     *  
     * @return \Papier\File\CrossReference\CrossReferenceSubsection
     */
    public function addSubsection()
    {
        $objects = $this->getSubsections();

        $object = new CrossReferenceSubsection();
        $objects[] = $object;

        $this->setObjects($objects);

        return $object;
    }

    /**
     * Get subsections.
     *  
     * @return array
     */
    public function getSubsections()
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
        $objects = $this->getSubsections();

        $value = 'xref'. self::EOL_MARKER;
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}