<?php

namespace Papier\File;

use Papier\Base\BaseObject;

use Papier\Object\IntegerObject;
use Papier\Object\NameObject;
use Papier\Object\DictionaryObject;

use Papier\File\CrossReference;
use Papier\Factory\Factory;

use Papier\Type\PageTreeType;
use Papier\Type\RectangleType;

use Papier\Validator\IntValidator;

class FileBody extends BaseObject
{
    /**
     * The offset of the body.
     *
     * @var int
     */
    private $offset = 0;

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
        $this->documentCatalog = Factory::getInstance()->createType('DocumentCatalog');

        $outlines = Factory::getInstance()->createType('Dictionary');
        
        $name = new NameObject();
        $name->setValue('Outlines');

        $count = new IntegerObject();
        $count->setValue(0);

        $outlines->setEntry('Type', $name);
        $outlines->setEntry('Count', $count);

        $pageTree = new PageTreeType();
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
     * Set body's offset.
     *  
     * @param  int  $offset
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\File\FileBody
     */
    public function setOffset($offset)
    {
        if (!IntValidator::isValid($offset, 0)) {
            throw new InvalidArgumentException("Offset is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->offset = $offset;
        return $this;
    } 

    /**
     * Add page to body.
     *
     * @return \Papier\Type\PageObjectType
     */
    public function addPage()
    {
        $page = $this->getPageTree()->addObject();

        $contents = Factory::getInstance()->createObject('Stream');
        $contents->setContent('...Page-marking operators...');
        
        $resources = new DictionaryObject();
        $resources->setIndirect(false);

        $procset = Factory::getInstance()->createObject('Array');

        $pdf = new NameObject();
        $pdf->setValue('PDF');

        $procset->append($pdf);

        $resources->setEntry('ProcSet', $procset);

        $mediabox = new RectangleType();
        $mediabox->setCoordinates([0.0, 0.0, 612.0, 792.0]);

        $page->setParent($this->getPageTree());
        $page->setContents($contents);
        $page->setMediaBox($mediabox);
        $page->setResources($resources);

        return $page;
    } 

    /**
     * Get body's offset.
     *
     * @return int
     */
    protected function getOffset()
    {
        return $this->offset;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $crossreference = CrossReference::getInstance();

        $offset = $this->getOffset();

        $objects = $crossreference->getObjects();
        
        $content = '';
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $content .= $object->getObject() . self::EOL_MARKER;
            }
        }

        $content .= $crossreference->setOffset($offset)->write();

        return $content;
    }
}