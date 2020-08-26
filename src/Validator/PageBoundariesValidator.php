<?php

namespace Papier\Validator;

use Papier\Document\PageBoundaries;
use Papier\Validator\StringValidator;

class PageBoundariesValidator extends StringValidator
{
    /**
     * Page boundaries types.
     *
     * @var array
     */
    const PAGE_BOUNDARIES_TYPES = array(
        PageBoundaries::MEDIABOX,
        PageBoundaries::CROPBOX,
        PageBoundaries::TRIMBOX,
        PageBoundaries::BLEEDBOX,
        PageBoundaries::ARTBOX,
    );


     /**
     * Test if given parameter is a valid page mode.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::PAGE_BOUNDARIES_TYPES);
        return $isValid;
    }
}