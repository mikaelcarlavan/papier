<?php

namespace Papier\Widget;


use Papier\Object\BaseObject;
use Papier\Type\ArrayType;
use Papier\Type\ContentStreamType;
use Papier\Type\PageObjectType;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;
use Papier\Papier;

abstract class BaseWidget
{
    /**
     * The parent of the widget
     *
     * @var PageObjectType
     */
    protected PageObjectType $page;

    /**
     * Set widget's page.
     *
     * @param  PageObjectType  $page
     * @return BaseWidget
     */
    public function setPage(PageObjectType $page): BaseWidget
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get widget's page.
     *
     * @return PageObjectType
     */
    public function getPage(): PageObjectType
    {
        return $this->page;
    }

    /**
     * Get contents.
     *
     * @return ContentStreamType
     */
    public function getContents(): ContentStreamType
    {
        $page = $this->getPage();
        return $page->getContents();
    }

    /**
     * Format widget's content.
     *
     * @return BaseWidget
     */
    abstract function format(): BaseWidget;
}