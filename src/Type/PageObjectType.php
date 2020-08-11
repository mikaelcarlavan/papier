<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\IntegerObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;

use Papier\Type\DateType;

use Papier\Validator\IntValidator;
use Papier\Validator\BoolValidator;

use InvalidArgumentException;

class PageObjectType extends DictionaryObject
{
    /**
     * Set parent.
     *  
     * @param  \Papier\Type\PageTreeNodeType  $parent
     * @throws InvalidArgumentException if the provided argument is not of type 'PageTreeNodeType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setParent($parent)
    {
        if (!$parent instanceof PageTreeNodeType) {
            throw new InvalidArgumentException("Parent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setObjectForKey('Parent', $parent);
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return \Papier\Type\PageObjectType
     */
    public function getParent()
    {
        return $this->getObjectForKey('Parent');
    } 

    /**
     * Set date and time of last object's modification.
     *  
     * @param  \Papier\Type\DateType  $date
     * @throws InvalidArgumentException if the provided argument is not of type 'DateType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setLastModified($date)
    {
        if (!$date instanceof DateType) {
            throw new InvalidArgumentException("ExtGState is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('LastModified', $date);
        return $this;
    } 

    /**
     * Set resources.
     *  
     * @param  \Papier\Object\DictionaryObject  $resources
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setResources($resources)
    {
        if (!$resources instanceof DictionaryObject) {
            throw new InvalidArgumentException("Resources is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Resources', $resources);
        return $this;
    } 

    /**
     * Set boundaries of the physical medium on which the page shall be displayed or printed.
     *  
     * @param  \Papier\Type\RectangleType  $mediabox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setMediaBox($mediabox)
    {
        if (!$mediabox instanceof RectangleType) {
            throw new InvalidArgumentException("MediaBox is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('MediaxBox', $mediabox);
        return $this;
    }

    /**
     * Set the visible region of default user space.
     *  
     * @param  \Papier\Type\RectangleType  $cropbox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setCropBox($cropbox)
    {
        if (!$cropbox instanceof RectangleType) {
            throw new InvalidArgumentException("CropBox is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('CropBox', $cropbox);
        return $this;
    }

    /**
     * Set region to which the contents of the page shall be clipped when output in a production enviroment.
     *  
     * @param  \Papier\Type\RectangleType  $bleedbox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setBleedBox($bleedbox)
    {
        if (!$bleedbox instanceof RectangleType) {
            throw new InvalidArgumentException("BleedBox is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('BleedBox', $bleedbox);
        return $this;
    }

    /**
     * Set intended dimensions of the finished page after trimming.
     *  
     * @param  \Papier\Type\RectangleType  $trimbox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setTrimBox($trimbox)
    {
        if (!$trimbox instanceof RectangleType) {
            throw new InvalidArgumentException("TrimBox is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('TrimBox', $trimbox);
        return $this;
    }

    /**
     * Set extend of the page's meaningful content.
     *  
     * @param  \Papier\Type\RectangleType  $artbox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\PageObjectType
     */
    public function setArtBox($artbox)
    {
        if (!$artbox instanceof RectangleType) {
            throw new InvalidArgumentException("ArtBox is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('ArtBox', $artbox);
        return $this;
    }

    /**
     * Set colours and other visual characteristics.
     *  
     * @param  \Papier\Object\DictionaryObject  $boxcolorinfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setBoxColorInfo($boxcolorinfo)
    {
        if (!$boxcolorinfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("BoxColorInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('BoxColorInfo', $boxcolorinfo);
        return $this;
    }


    /**
     * Set the contents of the page.
     *  
     * @param  mixed  $contents
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject' or 'ArrayObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setContents($contents)
    {
        if (!$contents instanceof StreamObject && !$contents instanceof ArrayObject) {
            throw new InvalidArgumentException("Contents is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Contents', $contents);
        return $this;
    }

    /**
     * Set the number of degrees by which the page should be rotated before printed or displayed.
     *  
     * @param  \Papier\Object\IntegerObject  $rotate
     * @throws InvalidArgumentException if the provided argument is not of type 'IntegerObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setRotate($rotate)
    {
        if (!$rotate instanceof IntegerObject) {
            throw new InvalidArgumentException("Rotate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Rotate', $rotate);
        return $this;
    }

    /**
     * Set attributes of the page's page group for use in the transparent imaging model.
     *  
     * @param  \Papier\Object\DictionaryObject  $boxcolorinfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setGroup($group)
    {
        if (!$group instanceof DictionaryObject) {
            throw new InvalidArgumentException("Group is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Group', $group);
        return $this;
    }


    /**
     * Set the thumbnail image of the page.
     *  
     * @param  \Pepier\Object\StreamObject  $contents
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setThumb($thumb)
    {
        if (!$thumb instanceof StreamObject) {
            throw new InvalidArgumentException("Thumb is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Thumb', $thumb);
        return $this;
    }

    /**
     * Set references to articles beads.
     *  
     * @param  mixed  $b
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\PageObjectType
     */
    public function setB($b)
    {
        if (!$b instanceof ArrayObject) {
            throw new InvalidArgumentException("B is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('B', $b);
        return $this;
    }
}