<?php

namespace Papier\Validator;

use Papier\Document\PageBoundaries;

class PageBoundariesValidator extends StringValidator
{
    /**
     * Page boundaries types.
     *
     * @var array
     */
    const PAGE_BOUNDARIES_TYPES = array(
        PageBoundaries::MEDIA_BOX,
        PageBoundaries::CROP_BOX,
        PageBoundaries::TRIM_BOX,
        PageBoundaries::BLEED_BOX,
        PageBoundaries::ART_BOX,
    );


     /**
     * Test if given parameter is a valid page mode.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::PAGE_BOUNDARIES_TYPES);
    }
}