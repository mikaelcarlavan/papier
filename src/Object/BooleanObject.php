<?php

namespace Papier\Object;

use Papier\Base\Object;

class BooleanObject extends Object
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
}