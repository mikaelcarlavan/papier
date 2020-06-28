<?php

namespace Papier\Object;

use Papier\Object\Base\IndirectObject;
use Countable;

class ArrayObject extends IndirectObject implements Countable
{

    /**
     * Create a new ArrayObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = array();
        parent::__construct();
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
     * Add object to array.
     *  
     * @param  mixed  $object
     * @return \Papier\Object\ArrayObject
     */
    protected function addObject($object)
    {
        $objects = $this->getValue();

        $arr = is_array($objects) ? $objects : array($objects);
        $arr[] = $object;

        return $this->setValue($arr);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getValue() ?? array();
        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $object) {
                $value .= ' '.$object->format();
            }
        }

        return '['.$value.']';
    }
}