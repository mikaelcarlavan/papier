<?php

namespace Papier\Graphics;

use InvalidArgumentException;
use Papier\Factory\Factory;

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
        $state = sprintf('%s Do', Factory::create('Papier\Type\Base\NameType', $xobject)->format());
        return $this->addToContent($state);
    } 
}