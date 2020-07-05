<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSubsection;

class CrossReferenceSection extends ArrayObject
{
    /**
     * Magical method.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @throws InvalidArgumentException if the provided argument does not inherit 'CrossReferenceSubsection'.
     * @return \Papier\File\CrossReference\CrossReferenceSection
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
     * @param  string  $key
     * @return \Papier\File\CrossReference\CrossReferenceSubsection
     */
    public function __get($key)
    {
        $this->getObjectForKey($key);
    }

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