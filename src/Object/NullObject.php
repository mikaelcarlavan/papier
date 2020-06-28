<?php

namespace Papier\Object;

use Papier\Object\Base\IndirectObject;

class NullObject extends IndirectObject
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