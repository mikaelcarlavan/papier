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

use Papier\Graphics\DeviceColourSpace;

use Papier\Document\ProcedureSet;

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
     * @return \Papier\Type\PageTreeNodeType
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

        $pdf = Factory::create('Name', ProcedureSet::GRAPHICS);
        $text = Factory::create('Name', ProcedureSet::TEXT);
        $imageb = Factory::create('Name', ProcedureSet::GRAYSCALE_IMAGES);

        $procset = Factory::create('Array', null, true)
            ->append($pdf)
            ->append($text)
            ->append($imageb);

        $helvetica = Factory::create('Type1Font', null, true)
            ->setName('F1')
            ->setBaseFont('Helvetica');

        $font = Factory::create('Dictionary')->setEntry('F1', $helvetica);

        
        $image = Factory::create('Image', null, true);
        $image->setWidth(256);
        $image->setHeight(320);
        $image->setColorSpace(DeviceColourSpace::GRAY);
        $image->setBitsPerComponent(8);
        $image->setContent(\file_get_contents('image.bmp'));

        $xobject = Factory::create('Dictionary')->setEntry('Im1', $image);

        $page->setParent($this->getPageTree());

        $resources = $page->getResources();
        $resources->setEntry('ProcSet', $procset);
        $resources->setEntry('Font', $font);
        $resources->setEntry('XObject', $xobject);

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