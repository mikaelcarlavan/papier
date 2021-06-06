<?php

namespace Papier\File;

use Papier\Base\BaseObject;

use Papier\Repository\Repository;
use Papier\Factory\Factory;
use Papier\Type\DocumentCatalogType;
use Papier\Type\PageObjectType;
use Papier\Type\PageTreeNodeType;
use Papier\Type\PageTreeType;


class FileBody extends BaseObject
{
     /**
     * Page tree
     *
     * @var PageTreeType
     */
    private $pageTree;

     /**
     * Document catalog
     *
     * @var DocumentCatalogType
     */
    private $documentCatalog;

    /**
     * Create a new object instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->documentCatalog = Factory::create('DocumentCatalog', null, true);

        $outlines = Factory::create('Dictionary', null, true);
        
        $pageTree = Factory::create('PageTree');

        $name = Factory::create('Name', 'Outlines');
        $count = Factory::create('Integer', 0);

        $outlines->setEntry('Type', $name);
        $outlines->setEntry('Count', $count);

        $this->pageTree = $pageTree->getNode();

        //$this->documentCatalog->setOutlines($outlines);
        $this->documentCatalog->setPages($this->pageTree);
    }

    /**
     * Get page tree.
     *
     * @return PageTreeNodeType
     */
    public function getPageTree()
    {
        return $this->pageTree;
    }

    /**
     * Get document catalog.
     *
     * @return DocumentCatalogType
     */
    public function getDocumentCatalog(): DocumentCatalogType
    {
        return $this->documentCatalog;
    }

    /**
     * Add page to body.
     *
     * @return PageObjectType
     */
    public function addPage(): PageObjectType
    {
        $page = $this->getPageTree()->addObject();
        $page->setParent($this->getPageTree());
        return $page;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $objects = Repository::getInstance()->getObjects();
                
        $content = '';
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $content .= $object->getObject();
            }
        }

        return $content;
    }
}