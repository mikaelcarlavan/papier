<?php

namespace Papier\File;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSection;

class CrossReferenceTable extends ArrayObject
{
    /**
     * Magical method.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @throws InvalidArgumentException if the provided argument does not inherit 'IndirectObject'.
     * @return \Papier\Object\DictionaryObject
     */
    public function __set($key, $object)
    {
        if (!$object instanceof CrossReferenceSection) {
            throw new InvalidArgumentException("Cross-reference section is incorrect. See ".__CLASS__." class's documentation for possible values.");
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
     * Add new section.
     *  
     * @return \Papier\Object\CrossReferenceSection
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