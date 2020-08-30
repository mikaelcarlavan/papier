<?php

namespace Papier\Validator;

use Papier\Text\RenderingMode;

use Papier\Validator\Base\Validator;
use Papier\Validator\IntegerValidator;

class RenderingModeValidator implements Validator
{
    /**
     * Rendering modes.
     *
     * @var array
     */
    const RENDERING_MODES = array(
        RenderingMode::FILL,
        RenderingMode::STROKE,
        RenderingMode::FILL_THEN_STROKE,
        RenderingMode::NEITHER_FILL_NOR_STROKE,
        RenderingMode::FILL_AND_ADD_TO_PATH,
        RenderingMode::STROKE_AND_ADD_TO_PATH,
        RenderingMode::FILL_THEN_STROKE_AND_ADD_TO_PATH,
        RenderingMode::ADD_TO_PATH,
    );


     /**
     * Test if given parameter is a valid rendering mode.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = IntegerValidator::isValid($value) && in_array($value, self::RENDERING_MODES);
        return $isValid;
    }
}