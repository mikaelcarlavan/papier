<?php

namespace Papier\Component\Base;


use Papier\Type\ContentStreamType;
use Papier\Type\PageObjectDictionaryType;

abstract class BaseComponent
{
    /**
     * The parent of the component
     *
     * @var PageObjectDictionaryType
     */
    protected PageObjectDictionaryType $page;

    /**
     * Set component's page.
     *
     * @param  PageObjectDictionaryType  $page
     * @return BaseComponent
     */
    public function setPage(PageObjectDictionaryType $page): BaseComponent
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get component's page.
     *
     * @return PageObjectDictionaryType
     */
    public function getPage(): PageObjectDictionaryType
    {
        return $this->page;
    }

    /**
     * Get contents.
     *
     * @return ContentStreamType
     */
    protected function getContents(): ContentStreamType
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