<?php

namespace Papier\Base;

use Papier\Base\Object;

class ArrayObject extends Object
{
    /**
     * Add object to array.
     *  
     * @param  mixed  $object
     * @return \Papier\Base\ArrayObject
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