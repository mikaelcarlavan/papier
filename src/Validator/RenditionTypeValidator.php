<?php

namespace Papier\Validator;

use Papier\Graphics\PaintType;
use Papier\Multimedia\Rendition;
use Papier\Type\RenditionDictionaryType;
use Papier\Validator\Base\Validator;

class RenditionTypeValidator implements Validator
{
    /**
     * Paint types.
     *
     * @var array<string>
     */
    const RENDITION_TYPES = array(
        Rendition::MEDIA_RENDITION_TYPE,
		Rendition::SELECTOR_RENDITION_TYPE,
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