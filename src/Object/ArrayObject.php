<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;

class ArrayObject extends DictionaryObject
{
    /**
     * Get first object.
     *  
     * @return IndirectObject
     */
    public function first() 
    {
        $objects = $this->getObjects();
        $keys = $this->getKeys();

        return $objects[$keys[0]];
    }

    /**
     * Get last object.
     *  
     * @return IndirectObject
     */
    public function last() 
    {
        $objects = $this->getObjects();
        $keys = $this->getKeys();
        
        return $objects[$keys[$this->count()-1]];
    }

    /**
     * Check if object has given value.
     *  
     * @param  string  $value
     * @return bool
     */
    public function has(string $value)
    {
        $objects = $this->getObjects();
        if (count($objects)) {
            foreach ($objects as $object) {
                if ($object->getValue() == $value) {
                    return true;
                }
            }
        }

        return false;
    }

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
     * @param IndirectObject $object
     * @return ArrayObject
     */
    public function append(IndirectObject $object)
    {
        $objects = $this->getObjects();
        $objects[] = $object;

        return $this->setObjects($objects);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getObjects();
        $value = '';

        if (is_array($objects) && count($objects) > 0) {
            foreach ($objects as $object) {
                $value .= ' '.$object->write();
            }         
        }

        return '[' .$value. ']';
    }
}