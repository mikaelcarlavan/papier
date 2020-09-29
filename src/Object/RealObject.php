<?php

namespace Papier\Object;

use Papier\Papier;

use Papier\Base\IndirectObject;
use Papier\Validator\RealValidator;

use InvalidArgumentException;

class RealObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @return RealObject
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    */
    public function setValue($value)
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        // Convert from scientific notation
        $value = number_format($value, Papier::MAX_DECIMALS);
        return parent::setValue($value);
    }    
}