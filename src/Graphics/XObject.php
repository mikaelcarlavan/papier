<?php

namespace Papier\Graphics;

use Papier\Validator\StringValidator;

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
        if (!StringValidator::isValid($xobject)) {
            throw new InvalidArgumentException("XObject is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s Do', Factory::create('Name', $xobject)->format());
        return $this->addToContent($state);
    } 
}