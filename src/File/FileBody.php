<?php

namespace Papier\File;

use Papier\Base\BaseObject;

use Papier\Object\IntegerObject;
use Papier\Object\NameObject;
use Papier\Object\DictionaryObject;

use Papier\Repository\Repository;
use Papier\Factory\Factory;

use Papier\Type\PageTreeType;
use Papier\Type\RectangleType;

use Papier\Validator\IntValidator;

class FileBody extends BaseObject
{
     /**
     * Page tree
     *
     * @var \Papier\Document\PageTree
     */
    private $pageTree;

     /**
     * Document catalog
     *
     * @var \Papier\Type\DocumentCatalogType
     */
    private $documentCatalog;

    /**
     * Create a new object instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->documentCatalog = Factory::create('DocumentCatalog');

        $outlines = Factory::create('Dictionary');
        
        $pageTree = Factory::create('PageTree', null, false);

        $name = Factory::create('Name', 'Outlines', false);
        $count = Factory::create('Integer', 0, false);

        $outlines->setEntry('Type', $name);
        $outlines->setEntry('Count', $count);

        $this->pageTree = $pageTree->getNode();

        $this->documentCatalog->setOutlines($outlines);
        $this->documentCatalog->setPages($this->pageTree);
    }

    /**
     * Get page tree.
     *
     * @return \Papier\Document\DocumentCatalog
     */
    public function getPageTree()
    {
        return $this->pageTree;
    }

    /**
     * Get document catalog.
     *
     * @return \Papier\Type\DocumentCatalogType
     */
    public function getDocumentCatalog()
    {
        return $this->documentCatalog;
    }

    /**
     * Add page to body.
     *
     * @return \Papier\Type\PageObjectType
     */
    public function addPage()
    {
        $page = $this->getPageTree()->addObject();

        $contents = Factory::create('Stream');
        $contents->setContent('...Page-marking operators...');

        $pdf = Factory::create('Name', 'PDF', false);
        $procset = Factory::create('Array')->append($pdf);

        $page->setParent($this->getPageTree());
        $page->setContents($contents);    
        $page->getResources()->setEntry('ProcSet', $procset);

        return $page;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
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