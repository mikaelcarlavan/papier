<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use InvalidArgumentException;
use Papier\Validator\NumbersArrayValidator;
use RuntimeException;
use Papier\Document\ProcedureSet;

class PageTreeNodeType extends DictionaryType
{
    /**
     * Set parent.
     *  
     * @param  PageTreeNodeType  $parent
     * @return PageTreeNodeType
     */
    public function setParent(PageTreeNodeType $parent): PageTreeNodeType
    {
        $this->setEntry('Parent', $parent);
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return PageTreeNodeType
     */
    public function getParent(): PageTreeNodeType
    {
		/** @var PageTreeNodeType $parent */
		$parent = $this->getEntry('Parent');
		return $parent;
    } 

    /**
     * Get kids.
     *  
     * @return ArrayType
     */
    public function getKids(): ArrayType
    {
        if (!$this->hasEntry('Kids')) {
            $kids = Factory::create('Papier\Type\ArrayType');
            $this->setEntry('Kids', $kids);
        }

		/** @var ArrayType $kids */
		$kids = $this->getEntry('Kids');
        return $kids;
    }

    
    /**
     * Add node to tree.
     *  
     * @return PageTreeNodeType
     */
    public function addNode(): PageTreeNodeType
    {
        $node = Factory::create('Papier\Type\PageTreeNodeType', null, true);
        $this->getKids()->append($node);

        return $node;
    }
   
    /**
     * Add object to node.
     *  
     * @return PageObjectType
     */
    public function addObject(): PageObjectType
    {
        $node = Factory::create('Papier\Type\PageObjectType', null, true);
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Set kids.
     *  
     * @param  ArrayObject  $kids
     * @return PageTreeNodeType
     */
    public function setKids(ArrayObject $kids): PageTreeNodeType
    {
        $this->setEntry('Kids', $kids);
        return $this;
    }

    /**
     * Set resources.
     *
     * @param DictionaryObject $resources
     * @return PageTreeNodeType
     */
    public function setResources(DictionaryObject $resources): PageTreeNodeType
    {
        $this->setEntry('Resources', $resources);
        return $this;
    }

    /**
     * Get resources.
     *
     * @return DictionaryType
     */
    public function getResources(): DictionaryType
    {
        if (!$this->hasEntry('Resources')) {
            $resources = Factory::create('Papier\Type\DictionaryType');
            $this->setResources($resources);
        }

		/** @var DictionaryType $resources */
		$resources = $this->getEntry('Resources');
		return $resources;
    }

    /**
     * Set boundaries of the physical medium on which the page shall be displayed or printed.
     *
     * @param RectangleType $mediabox
     * @return PageTreeNodeType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setMediaBox(RectangleType $mediabox): PageTreeNodeType
    {
        $this->setEntry('MediaBox', $mediabox);
        return $this;
    }

    /**
     * Get mediabox.
     *
     * @return RectangleType
     */
    public function getMediaBox(): RectangleType
    {
        if (!$this->hasEntry('MediaBox')) {
            $mediabox = Factory::create('Papier\Type\RectangleType');
            $this->setMediaBox($mediabox);
        }

		/** @var RectangleType $mediaBox */
		$mediaBox = $this->getEntry('MediaBox');
        return $mediaBox;
    }

    /**
     * Set the visible region of default user space.
     *
     * @param RectangleType $cropbox
     * @return PageTreeNodeType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setCropBox(RectangleType $cropbox): PageTreeNodeType
    {
        $this->setEntry('CropBox', $cropbox);
        return $this;
    }

    /**
     * Get cropbox.
     *
     * @return RectangleType
     */
    public function getCropBox(): RectangleType
    {
        if (!$this->hasEntry('CropBox')) {
            $cropbox = Factory::create('Papier\Type\RectangleType');
            $this->setCropBox($cropbox);
        }

		/** @var RectangleType $cropBox */
		$cropBox = $this->getEntry('CropBox');
        return $cropBox;
    }

    /**
     * Set the number of degrees by which the page should be rotated before printed or displayed.
     *
     * @param int $rotate
     * @return PageTreeNodeType
     */
    public function setRotate(int $rotate): PageTreeNodeType
    {
        $value = Factory::create('Papier\Type\IntegerType', $rotate);

        $this->setEntry('Rotate', $value);
        return $this;
    }

    /**
     * Format page tree's content.
     *
     * @return string
     */
    public function format(): string
    {
        /*        
        if (!$this->hasEntry('Parent')) {
            throw new RuntimeException("Parent is missing. See ".__CLASS__." class's documentation for possible values.");
        }*/

        $kids = $this->getKids();

        $num = count($kids);

        if ($num > 0) {
            $kid = $kids->first();

            $allObjects = $kid instanceof PageObjectType;
            $allNodes = $kid instanceof PageTreeNodeType;

            for ($i = 0; $i < $kids->count(); $i++) {
                $kid = $kids->current();

                if ($kid instanceof PageTreeNodeType && $allObjects) {
                    throw new RuntimeException("All kids should be of same type. See ".__CLASS__." class's documentation for possible values.");
                }

                if ($kid instanceof PageObjectType && $allNodes) {
                    throw new RuntimeException("All kids should be of same type. See ".__CLASS__." class's documentation for possible values.");
                }

                $kids->next();
            }
        }

        $type = Factory::create('Papier\Type\NameType', 'Pages');
        $count = Factory::create('Papier\Type\IntegerType', $num);

        $this->setEntry('Type', $type);
        $this->setEntry('Count', $count);

        return parent::format();
    }
}