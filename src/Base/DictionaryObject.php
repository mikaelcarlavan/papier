<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Base\NullObject;

class DictionaryObject extends Object
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
     * Get number of objects.
     *  
     * @return int
     */
    public function length()
    {
        $objects = $this->getValue();
        return count($objects);
    }

    /**
     * Set object for given key.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @return \Papier\Base\DictionaryObject
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
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getValue();

        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $name = new NameObject();
                $name->setValue($key);

                $value .= $name->format() .' ' .$object->format(). $this->EOL_MARKER;
            }
        }

        return '<<'.$value.'>>';
    }
}