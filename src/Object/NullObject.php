<?php

namespace Papier\Object;

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