<?php

namespace Papier\Validator;

use Papier\Graphics\PaintType;
use Papier\Type\RenditionType;
use Papier\Validator\Base\Validator;

class RenditionTypeValidator implements Validator
{
    /**
     * Paint types.
     *
     * @var array
     */
    const RENDITION_TYPES = array(
        RenditionType::MEDIA_RENDITION_TYPE,
        RenditionType::SELECTOR_RENDITION_TYPE,
    );


    /**
     * Test if given parameter is a valid rendition type.
     *
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return StringValidator::isValid($value) && in_array($value, self::RENDITION_TYPES);
    }
}