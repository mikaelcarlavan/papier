<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;

class BooleanObject extends IndirectObject
{
    /**
    * Set value to true.
    *
    * @return \Papier\Object\BooleanObject
    */
    public function setTrue()
    {
        return $this->setValue(true);
    }

    /**
     * Set value to false.
     *  
     * @return \Papier\Object\BooleanObject
     */
    public function setFalse()
    {
        return $this->setValue(false);
    }

    /**
     * Returns if boolean is true.
     *  
     * @return bool
     */
    public function isTrue()
    {
        return $this->getValue();
    }
    
    /**
     * Returns if boolean is false.
     *  
     * @return bool
     */
    public function isFalse()
    {
        return !$this->isTrue();
    } 
}