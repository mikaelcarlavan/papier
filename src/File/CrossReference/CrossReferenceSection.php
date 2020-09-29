<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;

class CrossReferenceSection extends ArrayObject
{
    /**
     * Add new subsection.
     *  
     * @return CrossReferenceSubsection
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
        return $this->getObjects();
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
        if (is_array($objects) && count($objects) > 0) {
            foreach ($objects as $key => $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}