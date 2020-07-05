<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Object\NullObject;

use Countable;

class DictionaryObject extends IndirectObject implements Countable
{
    /**
     * Create a new DictionaryObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = array();
        parent::__construct();
    }  

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
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }
        $this->setObjectForKey($key, $object);
    }

    /**
     * Magical method.
     *  
     * @param  string  $key
     * @return \Papier\Base\Object
     */
    public function __get($key)
    {
        $this->getObjectForKey($key);
    }

    /**
     * Check if object has given key.
     *  
     * @param  string  $key
     * @return bool
     */
    protected function hasKey($key)
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
     * @return \Papier\Base\Object
     */
    protected function getObjectForKey($key)
    {
        $objects = $this->getObjects();
        $object = $objects[$key] ?? new NullObject();

        return $object;
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
        return $objects;
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
            throw new InvalidArgumentException("Object's list is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        foreach ($objects as $object) {
            if (!$object instanceof IndirectObject) {
                throw new InvalidArgumentException("Object is incorrect. See ".get_class($this)." class's documentation for possible values.");
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
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $name = new NameObject();
                $name->setValue($key);

                $value .= $name->format() .' '. $object->write();
            }
        }

        return '<<' .$value. '>>';
    }
}