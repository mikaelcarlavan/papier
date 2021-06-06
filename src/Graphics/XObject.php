<?php

namespace Papier\Graphics;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait XObject
{
    /**
     * Paint external object.
     *  
     * @param  string  $xobject
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function paintXObject(string $xobject)
    {
        $state = sprintf('%s Do', Factory::create('Name', $xobject)->format());
        return $this->addToContent($state);
    } 
}