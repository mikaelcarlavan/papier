<?php

namespace Papier\Graphics;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

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
        if (!StringValidator::isValid($sh)) {
            throw new InvalidArgumentException("Shading is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s sh', Factory::create('Name', $sh)->format());
        return $this->addToContent($state);
    } 
}