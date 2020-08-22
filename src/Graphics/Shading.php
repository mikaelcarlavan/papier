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
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return mixed
     */
    public function setShading($sh)
    {
        if (!StringValidator::isValid($sh)) {
            throw new InvalidArgumentException("Shading is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s sh', Factory::create('Name', $ri)->format());
        return $this->addToContent($state);
    } 
}