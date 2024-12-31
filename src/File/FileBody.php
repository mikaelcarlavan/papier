<?php

namespace Papier\File;

use Papier\Factory\Factory;
use Papier\Object\BaseObject;
use Papier\Object\IndirectObject;
use Papier\Repository\Repository;
use Papier\Type\DocumentCatalogDictionaryType;
use Papier\Type\PageObjectDictionaryType;
use Papier\Type\PageTreeNodeDictionaryType;

class FileBody extends BaseObject
{
     /**
     * Page tree
     *
     * @var PageTreeNodeDictionaryType
     */
    private PageTreeNodeDictionaryType $pageTree;

     /**
     * Document catalog
     *
     * @var DocumentCatalogDictionaryType
     */
    private DocumentCatalogDictionaryType $documentCatalog;

    /**
     * Create a new object instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->documentCatalog = Factory::create('Papier\Type\DocumentCatalogDictionaryType', null, true);

        $outlines = Factory::create('Papier\Type\OutlineDictionaryType', null, true);
		$outlines->setCount(0);

        $pageTree = Factory::create('Papier\Type\PageTreeDictionaryType');

        $this->pageTree = $pageTree->getNode();

        $this->documentCatalog->setPages($this->pageTree);
		$this->documentCatalog->setOutlines($outlines);

    }

    /**
     * Get page tree.
     *
     * @return PageTreeNodeDictionaryType
     */
    public function getPageTree(): PageTreeNodeDictionaryType
    {
        return $this->pageTree;
    }

    /**
     * Get document catalog.
     *
     * @return DocumentCatalogDictionaryType
     */
    public function getDocumentCatalog(): DocumentCatalogDictionaryType
    {
        return $this->documentCatalog;
    }

    /**
     * Add page to body.
     *
     * @return PageObjectDictionaryType
     */
    public function addPage(): PageObjectDictionaryType
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

		/** @var array<IndirectObject> $objects */
        $objects = Repository::getInstance()->getObjects();

        $crossReference = CrossReference::getInstance();
        $table = $crossReference->getTable();

        $subsection = $table->addSection()->addSubsection();

        $subsection->addEntry()->setFree()->setGeneration(65535);

        $offset = $crossReference->getOffset();

        $content = '';

		foreach ($objects as $object) {
			$subsection->addEntry()->setOffset($offset);
			$obj = $object->getObject();
			$content .= $obj;
			$offset += strlen($obj);
		}

        return $content;
    }
}