<?php

namespace Papier\Base;

use Papier\Base\Object;

class BooleanObject extends Object
{
    /**
    * Set value to true.
    *
    * @return \Papier\Base\BooleanObject
    */
    public function setTrue()
    {
        return $this->setValue(true);
    }

    /**
     * Set value to false.
     *  
     * @return \Papier\Base\BooleanObject
     */
    public function setFalse()
    {
        return $this->setValue(false);
    }
}