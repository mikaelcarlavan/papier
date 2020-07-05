<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Validator\RealValidator;

use InvalidArgumentException;

class RealObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @param  int  $maxDecimals
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    * @return \Papier\Object\RealObject
    */
    public function setValue($value, $maxDecimals = 10)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        // Convert from scientific notation
        $value = number_format($value, $maxDecimals);
        return parent::setValue($value);
    }    
}