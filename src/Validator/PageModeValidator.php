<?php

namespace Papier\Validator;

use Papier\Document\PageMode;
use Papier\Validator\StringValidator;

class PageModeValidator implements StringValidator
{
    /**
     * Page modes.
     *
     * @var array
     */
    const PAGE_MODES = array(
        PageMode::USE_NONE_MODE,
        PageMode::USE_OUTLINES_MODE,
        PageMode::USE_THUMBS_MODE,
        PageMode::FULL_SCREEN_MODE,
        PageMode::USE_OC_MODE,
        PageMode::USE_ATTACHMENTS_MODE,
    );


     /**
     * Test if given parameter is a valid page mode.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::PAGE_MODES);
        return $isValid;
    }
}