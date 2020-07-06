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
     * Single page layout (display one page at a time)
     *
     * @var string
     */
    const SINGLE_PAGE_LAYOUT = 'SinglePage';
  
    /**
     * One column layout (display the page in one column)
     *
     * @var string
     */
    const ONE_COLUMN_LAYOUT = 'OneColumn';

    /**
     * Two column left layout (display the pages in tow columns, with odd-numbered pages on the left)
     *
     * @var string
     */
    const TWO_COLUMN_LEFT_LAYOUT = 'TwoColumnLeft';

    /**
     * Two column right layout (display the pages in tow columns, with odd-numbered pages on the right)
     *
     * @var string
     */
    const TWO_COLUMN_RIGHT_LAYOUT = 'TwoColumnRight';

    /**
     * Two page left layout (display the pages two at a time, with odd-numbered pages on the left)
     *
     * @var string
     */
    const TWO_PAGE_LEFT_LAYOUT = 'TwoPageLeft';

    /**
     * Two page right layout (display the pages two at a time, with odd-numbered pages on the right)
     *
     * @var string
     */
    const TWO_PAGE_RIGHT_LAYOUT = 'TwoPageRight';
}