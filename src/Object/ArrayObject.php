<?php

namespace Papier\Object;

use Papier\Object\DictionaryObject;
use Papier\Base\IndirectObject;

class ArrayObject extends DictionaryObject
{

    /**
     * Create a new ArrayObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = [];
        parent::__construct();
    }  

    /**
     * Get number of objects.
     *  
     * @return int
     */
    public function count()
    {
        $objects = $this->getObjects();
        return count($objects);
    }

    
    /**
     * Add object to array.
     *  
     * @param  \Papier\Base\IndirectObject  $object
     * @return \Papier\Object\ArrayObject
     */
    public function addObject($object)
    {
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $objects = $this->getObjects();

        $arr = is_array($objects) ? $objects : array($objects);
        $arr[] = $object;

        return $this->setObjects($arr);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getObjects() ?? array();
        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $object) {
                $value .= ' '.$object->write();
            }
        }

        return '[' .$value. ']';
    }
}