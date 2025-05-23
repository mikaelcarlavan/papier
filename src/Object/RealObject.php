<?php

namespace Papier\Object;

use InvalidArgumentException;
use Papier\Papier;
use Papier\Validator\RealValidator;

class RealObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param mixed $value
    * @return RealObject
    * @throws InvalidArgumentException if the provided argument is not of type 'float'.
    */
    public function setValue(mixed $value): RealObject
    {
        if (!RealValidator::isValid($value)) {
            throw new InvalidArgumentException("Real is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		/** @var float $value */
		// Convert from scientific notation
        $formattedValue = number_format($value, Papier::MAX_DECIMALS);
        parent::setValue($formattedValue);
        return $this;
    }    
}