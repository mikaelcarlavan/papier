<?php

namespace Papier\Object;

use Papier\Base\Object;

class NullObject extends Object
{

    /**
     * Create a new NullObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = null;
    }  
}