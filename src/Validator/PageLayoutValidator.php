<?php

namespace Papier\Validator;

use Papier\Document\PageLayout;

class PageLayoutValidator extends StringValidator
{
    /**
     * Page layouts.
     *
     * @var array<string>
     */
    const PAGE_LAYOUTS = array(
        PageLayout::SINGLE_PAGE_LAYOUT,
        PageLayout::ONE_COLUMN_LAYOUT,
        PageLayout::TWO_COLUMN_LEFT_LAYOUT,
        PageLayout::TWO_COLUMN_RIGHT_LAYOUT,
        PageLayout::TWO_PAGE_LEFT_LAYOUT,
        PageLayout::TWO_PAGE_RIGHT_LAYOUT,
    );

     /**
     * Test if given parameter is a valid page layout.
     * 
     * @param  string  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::PAGE_LAYOUTS);
    }
}