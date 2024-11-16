<?php

namespace Papier\Validator;

use Papier\Document\PageMode;

class PageModeValidator extends StringValidator
{
    /**
     * Page modes.
     *
     * @var array<string>
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
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::PAGE_MODES);
    }
}