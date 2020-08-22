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

use Papier\Validator\IntegerValidator;

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
        $this->documentCatalog = Factory::create('DocumentCatalog', null, true);

        $outlines = Factory::create('Dictionary', null, true);
        
        $pageTree = Factory::create('PageTree');

        $name = Factory::create('Name', 'Outlines');
        $count = Factory::create('Integer', 0);

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

        $pdf = Factory::create('Name', 'PDF');
        $text = Factory::create('Name', 'Text');

        $procset = Factory::create('Array', null, true)
            ->append($pdf)
            ->append($text);

        $helvetica = Factory::create('Type1Font', null, true)
            ->setName('F1')
            ->setBaseFont('Helvetica');

        $font = Factory::create('Dictionary')->setEntry('F1', $helvetica);

        $page->setParent($this->getPageTree());
        $page->getResources()->setEntry('ProcSet', $procset);
        $page->getResources()->setEntry('Font', $font);

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