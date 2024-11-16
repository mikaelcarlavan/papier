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
    public function addSubsection(): CrossReferenceSubsection
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
    public function getSubsections(): array
    {
        return $this->getObjects();
    }  

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $objects = $this->getSubsections();

        $value = 'xref'. self::EOL_MARKER;
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}