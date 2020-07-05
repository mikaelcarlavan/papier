<?php

namespace Papier\Document;

use Papier\Base\Object;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Validator\StringValidator;

use InvalidArgumentException;
use Exception;

class PageLayout extends Object
{
    /**
     * Single page layout
     *
     * @var string
     */
    const SINGLE_PAGE_LAYOUT = 'SinglePage';
  
    /**
     * One column layout
     *
     * @var string
     */
    const ONE_COLUMN_LAYOUT = 'OneColumn';

    /**
     * Two column left layout
     *
     * @var string
     */
    const TWO_COLUMN_LEFT_LAYOUT = 'TwoColumnLeft';

    /**
     * Two column right layout
     *
     * @var string
     */
    const TWO_COLUMN_RIGHT_LAYOUT = 'TwoColumnRight';

    /**
     * Two page left layout
     *
     * @var string
     */
    const TWO_PAGE_LEFT_LAYOUT = 'TwoPageLeft';

    /**
     * Two page right layout
     *
     * @var string
     */
    const TWO_PAGE_RIGHT_LAYOUT = 'TwoPageRight';
}