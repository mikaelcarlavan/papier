<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Base\BaseObject;

use Papier\Object\NullObject;

use Papier\Factory\Factory;

use InvalidArgumentException;

use Countable;
use Iterator;

class DictionaryObject extends IndirectObject implements Countable, Iterator
{
    /**
     * The value of the current position.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Create a new DictionaryObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = [];
        $this->position = 0;
    }  

    /**
     * Reset current position.
     *  
     */
    public function rewind() 
    {
        $this->position = 0;
    }

    /**
     * Get object at position.
     *  
     * @return int
     */
    public function current() 
    {
        $keys = $this->getKeys();
        $objects = $this->getObjects();
        return $objects[$keys[$this->position]];
    }

    /**
     * Get current position.
     *  
     * @return int
     */
    public function key() 
    {
        $keys = $this->getKeys();
        return $keys[$this->position];
    }

    /**
     * Increment current position.
     *  
     */
    public function next() 
    {
        ++$this->position;
    }

    /**
     * Check if object exist at current position.
     *  
     * @return bool
     */
    public function valid() 
    {
        $keys = $this->getKeys();
        return isset($keys[$this->position]);
    }

    /**
     * Check if object has given key.
     *  
     * @param  string  $key
     * @return bool
     */
    public function hasEntry($key)
    {
        $objects = $this->getObjects();
        return isset($objects[$key]);
    }

    /**
     * Set object for given key.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @return \Papier\Object\DictionaryObject
     */
    protected function setObjectForKey($key, $object)
    {
        $objects = $this->getObjects();
        $objects[$key] = $object;

        return $this->setObjects($objects);
    }

    /**
     * Get value for given key.
     *  
     * @param  string  $key
     * @return mixed
     */
    protected function getObjectForKey($key)
    {
        $objects = $this->getObjects();
        $object = $objects[$key] ?? new NullObject();

        return $object;
    }  
    

    /**
     * Set entry for given key.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Object\DictionaryObject
     */
    public function setEntry($key, $object)
    {
        $this->setObjectForKey($key, $object);
        return $this;
    }

    /**
     * Get entry from dictionary.
     *      
     * @param  string  $key
     * @return mixed
     */
    public function getEntry($key)
    {
        return $this->getObjectForKey($key);
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
     * Get objects.
     *  
     * @return array
     */
    public function getObjects()
    {
        $objects = $this->getValue();
        return is_array($objects) ? $objects : array($objects);
    }

    /**
     * Get keys.
     *  
     * @return array
     */
    public function getKeys()
    {
        $objects = $this->getObjects();
        return array_keys($objects);
    }

    /**
     * Erase objects.
     *  
     * @return array
     */
    public function clearObjects()
    {
        return $this->clearValue();
    }

    /**
     * Set objects.
     * 
     * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the argument does not inherit 'IndirectObject'.
     * @return \Papier\Object\DictionaryObject
     */
    protected function setObjects($objects)
    {
        if (!is_array($objects)) {
            throw new InvalidArgumentException("Object's list is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        foreach ($objects as $object) {
            if (!$object instanceof BaseObject) {
                throw new InvalidArgumentException("Object is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }
        
        $this->setValue($objects);
        return $this;
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
        foreach ($objects as $key => $object) {
            $name = Factory::create('Name', $key, false);
            $value .= $name->format() .' '. $object->write();
        }      

        return '<< ' .$value. '>>';
    }
}