<?php

namespace Papier\File;

use Papier\Factory\Factory;
use Papier\Object\BaseObject;
use Papier\Repository\Repository;
use Papier\Type\DocumentCatalogType;
use Papier\Type\PageObjectType;
use Papier\Type\PageTreeNodeType;

class FileBody extends BaseObject
{
     /**
     * Page tree
     *
     * @var PageTreeNodeType
     */
    private PageTreeNodeType $pageTree;

     /**
     * Document catalog
     *
     * @var DocumentCatalogType
     */
    private DocumentCatalogType $documentCatalog;

    /**
     * Create a new object instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->documentCatalog = Factory::create('Papier\Type\DocumentCatalogType', null, true);

        $outlines = Factory::create('Papier\Type\DictionaryType', null, true);
        
        $pageTree = Factory::create('Papier\Type\PageTreeType');

        $name = Factory::create('Papier\Type\NameType', 'Outlines');
        $count = Factory::create('Papier\Type\IntegerType', 0);

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
    public function getPageTree(): PageTreeNodeType
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

        $crossReference = CrossReference::getInstance();
        $table = $crossReference->getTable();

        $subsection = $table->addSection()->addSubsection();

        $subsection->addEntry()->setFree()->setGeneration(65535);

        $offset = $crossReference->getOffset();

        $content = '';
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $subsection->addEntry()->setOffset($offset);
                $obj = $object->getObject();
                $content .= $obj;
                $offset += strlen($obj);
            }
        }

        return $content;
    }
}