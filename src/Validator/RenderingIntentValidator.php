<?php

namespace Papier\Validator;

use Papier\Graphics\RenderingIntent;

class RenderingIntentValidator extends StringValidator
{
    /**
     * Rendering intents.
     *
     * @var array
     */
    const RENDERING_INTENTS = array(
        RenderingIntent::ABSOLUTE_COLORIMETRIC,
        RenderingIntent::RELATIVE_COLORIMETRIC,
        RenderingIntent::SATURATION,
        RenderingIntent::PERCEPTUAL,
    );


     /**
     * Test if given parameter is a valid rendering intent.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::RENDERING_INTENTS);
    }
}