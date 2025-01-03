<?php

namespace Papier\Validator;

use Papier\Graphics\ColourComponents;

use Papier\Validator\Base\Validator;
use Papier\Validator\IntegerValidator;

class ColourComponentsValidator implements Validator
{
    /**
     * Number of components allowed values.
     *
     * @var array<int>
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
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value) && in_array($value, self::COMPONENTS_VALUES);
    }
}