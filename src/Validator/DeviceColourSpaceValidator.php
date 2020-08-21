<?php

namespace Papier\Validator;

use Papier\Graphics\DeviceColourSpace;
use Papier\Validator\StringValidator;

class DeviceColourSpaceValidator extends StringValidator
{
    /**
     * Device colour spaces.
     *
     * @var array
     */
    const DEVICE_COLOUR_SPACES = array(
        DeviceColourSpace::GRAY,
        DeviceColourSpace::RGB,
        DeviceColourSpace::CMYK,
    );


     /**
     * Test if given parameter is a valid device colour space.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::DEVICE_COLOUR_SPACES);
        return $isValid;
    }
}