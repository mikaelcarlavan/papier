<?php

namespace Papier\Object;

use Papier\Object\DictionaryObject;
use Papier\Base\IndirectObject;

use InvalidArgumentException;

class ArrayObject extends DictionaryObject
{
    /**
     * Get object at position.
     *  
     * @return int
     */
    public function current() 
    {
        $objects = $this->getObjects();
        return $objects[$this->position];
    }

    /**
     * Get current position.
     *  
     * @return int
     */
    public function key() 
    {
        return $this->position;
    }

    /**
     * Check if object exist at current position.
     *  
     * @return bool
     */
    public function valid() 
    {
        $objects = $this->getObjects();
        return isset($objects[$this->position]);
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
     * Append object to array.
     *  
     * @param  \Papier\Base\IndirectObject  $object
     * @return \Papier\Object\ArrayObject
     */
    public function append($object)
    {
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".__CLASS__." class's documentation for possible values.");
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