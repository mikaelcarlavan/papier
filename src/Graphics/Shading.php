<?php

namespace Papier\Graphics;

use InvalidArgumentException;
use Papier\Factory\Factory;

trait Shading
{
    /**
     * Set shape and colour shading.
     *  
     * @param  string  $sh
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setShading(string $sh)
    {
        $state = sprintf('%s sh', Factory::create('Papier\Type\Base\NameType', $sh)->format());
        return $this->addToContent($state);
    } 
}