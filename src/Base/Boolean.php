<?php

namespace Papier\Base;

use Papier\Base\Object;

class Boolean extends Object
{
    /**
     * Set value to true.
     *  
     * @return \Papier\Base\Boolean
     */
    public function setTrue()
    {
        return $this->setValue(true);
    }

    /**
     * Set value to false.
     *  
     * @return \Papier\Base\Boolean
     */
    public function setFalse()
    {
        return $this->setValue(false);
    }
}