<?php

namespace Papier\Component;


use Papier\Object\BaseObject;
use Papier\Type\ArrayType;
use Papier\Type\ContentStreamType;
use Papier\Type\PageObjectType;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;
use Papier\Papier;

abstract class BaseComponent
{
    /**
     * The parent of the component
     *
     * @var PageObjectType
     */
    protected PageObjectType $page;

    /**
     * Set component's page.
     *
     * @param  PageObjectType  $page
     * @return BaseComponent
     */
    public function setPage(PageObjectType $page): BaseComponent
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get component's page.
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
     * Format component's content.
     *
     * @return BaseComponent
     */
    abstract function format(): BaseComponent;
}