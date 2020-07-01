<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSubsection;

class CrossReferenceSection extends ArrayObject
{
    /**
     * Magical method.
     *  
     */
    public function __set($key, $object)
    {
        if (!$object instanceof CrossReferenceSubsection) {
            throw new InvalidArgumentException("Cross-reference subsection is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }
        $this->setObjectForKey($key, $object);
    }

    /**
     * Magical method.
     *  
     */
    public function __get($key)
    {
        $this->getObjectForKey($key);
    }

    /**
     * Add new subsection.
     *  
     * @return \Papier\Object\CrossReferenceSubsection
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