<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use RuntimeException;

class PageTreeNodeDictionaryType extends DictionaryType
{
    /**
     * Set parent.
     *  
     * @param  PageTreeNodeDictionaryType  $parent
     * @return PageTreeNodeDictionaryType
     */
    public function setParent(PageTreeNodeDictionaryType $parent): PageTreeNodeDictionaryType
    {
        $this->setEntry('Parent', $parent);
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return PageTreeNodeDictionaryType
     */
    public function getParent(): PageTreeNodeDictionaryType
    {
		/** @var PageTreeNodeDictionaryType $parent */
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
            $kids = Factory::create('Papier\Type\Base\ArrayType');
            $this->setEntry('Kids', $kids);
        }

		/** @var ArrayType $kids */
		$kids = $this->getEntry('Kids');
        return $kids;
    }

    
    /**
     * Add node to tree.
     *  
     * @return PageTreeNodeDictionaryType
     */
    public function addNode(): PageTreeNodeDictionaryType
    {
        $node = Factory::create('Papier\Type\PageTreeNodeDictionaryType', null, true);
        $this->getKids()->append($node);

        return $node;
    }
   
    /**
     * Add object to node.
     *  
     * @return PageObjectDictionaryType
     */
    public function addObject(): PageObjectDictionaryType
    {
        $node = Factory::create('Papier\Type\PageObjectDictionaryType', null, true);
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Set kids.
     *  
     * @param  ArrayObject  $kids
     * @return PageTreeNodeDictionaryType
     */
    public function setKids(ArrayObject $kids): PageTreeNodeDictionaryType
    {
        $this->setEntry('Kids', $kids);
        return $this;
    }

    /**
     * Set resources.
     *
     * @param DictionaryObject $resources
     * @return PageTreeNodeDictionaryType
     */
    public function setResources(DictionaryObject $resources): PageTreeNodeDictionaryType
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
            $resources = Factory::create('Papier\Type\Base\DictionaryType');
            $this->setResources($resources);
        }

		/** @var DictionaryType $resources */
		$resources = $this->getEntry('Resources');
		return $resources;
    }

    /**
     * Set boundaries of the physical medium on which the page shall be displayed or printed.
     *
     * @param RectangleNumbersArrayType $mediabox
     * @return PageTreeNodeDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setMediaBox(RectangleNumbersArrayType $mediabox): PageTreeNodeDictionaryType
    {
        $this->setEntry('MediaBox', $mediabox);
        return $this;
    }

    /**
     * Get mediabox.
     *
     * @return RectangleNumbersArrayType
     */
    public function getMediaBox(): RectangleNumbersArrayType
    {
        if (!$this->hasEntry('MediaBox')) {
            $mediabox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setMediaBox($mediabox);
        }

		/** @var RectangleNumbersArrayType $mediaBox */
		$mediaBox = $this->getEntry('MediaBox');
        return $mediaBox;
    }

    /**
     * Set the visible region of default user space.
     *
     * @param RectangleNumbersArrayType $cropbox
     * @return PageTreeNodeDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setCropBox(RectangleNumbersArrayType $cropbox): PageTreeNodeDictionaryType
    {
        $this->setEntry('CropBox', $cropbox);
        return $this;
    }

    /**
     * Get cropbox.
     *
     * @return RectangleNumbersArrayType
     */
    public function getCropBox(): RectangleNumbersArrayType
    {
        if (!$this->hasEntry('CropBox')) {
            $cropbox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setCropBox($cropbox);
        }

		/** @var RectangleNumbersArrayType $cropBox */
		$cropBox = $this->getEntry('CropBox');
        return $cropBox;
    }

    /**
     * Set the number of degrees by which the page should be rotated before printed or displayed.
     *
     * @param int $rotate
     * @return PageTreeNodeDictionaryType
     */
    public function setRotate(int $rotate): PageTreeNodeDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $rotate);

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

            $allObjects = $kid instanceof PageObjectDictionaryType;
            $allNodes = $kid instanceof PageTreeNodeDictionaryType;

            for ($i = 0; $i < $kids->count(); $i++) {
                $kid = $kids->current();

                if ($kid instanceof PageTreeNodeDictionaryType && $allObjects) {
                    throw new RuntimeException("All kids should be of same type. See ".__CLASS__." class's documentation for possible values.");
                }

                if ($kid instanceof PageObjectDictionaryType && $allNodes) {
                    throw new RuntimeException("All kids should be of same type. See ".__CLASS__." class's documentation for possible values.");
                }

                $kids->next();
            }
        }

        $type = Factory::create('Papier\Type\Base\NameType', 'Pages');
        $count = Factory::create('Papier\Type\Base\IntegerType', $num);

        $this->setEntry('Type', $type);
        $this->setEntry('Count', $count);

        return parent::format();
    }
}