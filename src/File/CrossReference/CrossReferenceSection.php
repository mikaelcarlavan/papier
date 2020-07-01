<?php

namespace Papier\File\CrossReference;

use Papier\Base\Object;

use Countable;

class CrossReferenceSection extends Object implements Countable
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
     */
    public function __set($key, $object)
    {
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
     * Set object for given key.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @return \Papier\Object\DictionaryObject
     */
    protected function setObjectForKey($key, $object)
    {
        $objects = $this->getValue();
        $objects[$key] = $object;

        return $this->setValue($objects);
    }

    /**
     * Get value for given key.
     *  
     * @param  string  $key
     * @return \Papier\Base\Object
     */
    protected function getObjectForKey($key)
    {
        $objects = $this->getValue();
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
        $objects = $this->getValue();
        return count($objects);
    }
    
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getValue();

        $value = 'xref'. $this->EOL_MARKER;
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}