<?php

namespace Papier\Graphics;

use Papier\Validator\NumberValidator;

use InvalidArgumentException;

trait Colour
{
    use DeviceColour;

    /**
     * Set colour for stroking operations in special color spaces.
     *  
     * @param   mixed   $values
     * @return mixed
     */
    public function setSpecialSpacesStrokingColor(...$values)
    {
        foreach ($values as $value) {
            if (!NumberValidator::isValid($value)) {
                throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        $state = sprintf('%s SCN', implode(' ', $values));
        return $this->addToContent($state);
    }

    /**
     * Set colour for non-stroking operations.
     *  
     * @param   mixed   $values
     * @return mixed
     */
    public function setSpecialSpacesNonStrokingColor(...$values)
    {
        foreach ($values as $value) {
            if (!NumberValidator::isValid($value)) {
                throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        $state = sprintf('%s scn', implode(' ', $values));
        return $this->addToContent($state);
    }
}