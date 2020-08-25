<?php

namespace Papier\Validator;

use Papier\Graphics\ColourComponents;
use Papier\Validator\IntegerValidator;

class ColourComponentsValidator extends IntegerValidator
{
    /**
     * Number of components allowed values.
     *
     * @var array
     */
    const COMPONENTS_VALUES = array(
        ColourComponents::ONE,
        ColourComponents::THREE,
        ColourComponents::FOUR,
    );


     /**
     * Test if given parameter is a valid number of components.
     * 
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::COMPONENTS_VALUES);
        return $isValid;
    }
}