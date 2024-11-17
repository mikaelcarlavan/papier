<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;

use Papier\Validator\NumberValidator;
use Papier\Validator\TabOrderValidator;
use Papier\Validator\NumbersArrayValidator;

use Papier\Factory\Factory;


use InvalidArgumentException;

class PageObjectType extends DictionaryType
{
    /**
     * Set parent.
     *  
     * @param PageTreeNodeType $parent
     * @return PageObjectType
     */
    public function setParent(PageTreeNodeType $parent): PageObjectType
    {
        $this->setEntry('Parent', $parent);
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return PageObjectType
      */
    public function getParent(): PageObjectType
    {
        return $this->getEntry('Parent');
    } 

    /**
     * Set date and time of last object's modification.
     *  
     * @param DateType $date
     * @return PageObjectType
     */
    public function setLastModified(DateType $date): PageObjectType
    {
        $this->setEntry('LastModified', $date);
        return $this;
    } 

    /**
     * Set resources.
     *  
     * @param DictionaryObject $resources
     * @return PageObjectType
     */
    public function setResources(DictionaryObject $resources): PageObjectType
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

        return $this->getEntry('Resources');
    }
    
    /**
     * Set boundaries of the physical medium on which the page shall be displayed or printed.
     *  
     * @param RectangleType $mediabox
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setMediaBox(RectangleType $mediabox): PageObjectType
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

        return $this->getEntry('MediaBox');
    }

    /**
     * Set the visible region of default user space.
     *  
     * @param RectangleType $cropbox
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setCropBox(RectangleType $cropbox): PageObjectType
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

        return $this->getEntry('CropBox');
    }

    /**
     * Set region to which the contents of the page shall be clipped when output in a production enviroment.
     *  
     * @param RectangleType $bleedbox
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setBleedBox(RectangleType $bleedbox): PageObjectType
    {
        $this->setEntry('BleedBox', $bleedbox);
        return $this;
    }

    /**
     * Get bleedbox.
     *  
     * @return RectangleType
     */
    public function getBleedBox(): RectangleType
    {
        if (!$this->hasEntry('BleedBox')) {
            $bleedbox = Factory::create('Papier\Type\RectangleType');
            $this->setBleedBox($bleedbox);
        }

        return $this->getEntry('BleedBox');
    }

    /**
     * Set intended dimensions of the finished page after trimming.
     *  
     * @param RectangleType $trimbox
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setTrimBox(RectangleType $trimbox): PageObjectType
    {
        $this->setEntry('TrimBox', $trimbox);
        return $this;
    }

    /**
     * Get trimbox.
     *  
     * @return RectangleType
     */
    public function getTrimBox(): RectangleType
    {
        if (!$this->hasEntry('TrimBox')) {
            $trimbox = Factory::create('Papier\Type\RectangleType');
            $this->setTrimBox($trimbox);
        }

        return $this->getEntry('TrimBox');
    }

    /**
     * Set extend of the page's meaningful content.
     *  
     * @param RectangleType $artbox
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setArtBox(RectangleType $artbox): PageObjectType
    {
        $this->setEntry('ArtBox', $artbox);
        return $this;
    }

    /**
     * Get artbox.
     *  
     * @return RectangleType
     */
    public function getArtBox(): RectangleType
    {
        if (!$this->hasEntry('ArtBox')) {
            $artbox = Factory::create('Papier\Type\RectangleType');
            $this->setArtBox($artbox);
        }

        return $this->getEntry('ArtBox');
    }

    /**
     * Set colours and other visual characteristics.
     *  
     * @param DictionaryObject $boxcolorinfo
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setBoxColorInfo(DictionaryObject $boxcolorinfo): PageObjectType
    {
        $this->setEntry('BoxColorInfo', $boxcolorinfo);
        return $this;
    }


    /**
     * Set the contents of the page.
     *  
     * @param  mixed  $contents
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject' or 'ArrayObject'.
     * @return PageObjectType
     */
    public function setContents($contents): PageObjectType
    {
        if (!$contents instanceof StreamObject && !$contents instanceof ArrayObject) {
            throw new InvalidArgumentException("Contents is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Contents', $contents);
        return $this;
    }

    /**
     * Get contents.
     *
     * @return ContentStreamType
     */
    public function getContents(): ContentStreamType
    {
        if (!$this->hasEntry('Contents')) {
            $contents = Factory::create('Papier\Type\ContentStreamType', null, true);
            $this->setContents($contents);
        }

        return $this->getEntry('Contents');
    }

    /**
     * Set the number of degrees by which the page should be rotated before printed or displayed.
     *  
     * @param int $rotate
     * @return PageObjectType
     */
    public function setRotate(int $rotate): PageObjectType
    {
        $value = Factory::create('Papier\Type\IntegerType', $rotate);

        $this->setEntry('Rotate', $value);
        return $this;
    }

    /**
     * Set attributes of the page's page group for use in the transparent imaging model.
     *  
     * @param DictionaryObject $group
     * @return PageObjectType
     */
    public function setGroup(DictionaryObject $group): PageObjectType
    {
        $this->setEntry('Group', $group);
        return $this;
    }


    /**
     * Set the thumbnail image of the page.
     *  
     * @param  StreamObject  $thumb
     * @return PageObjectType
     */
    public function setThumb(StreamObject $thumb): PageObjectType
    {
        $this->setEntry('Thumb', $thumb);
        return $this;
    }

    /**
     * Set references to articles beads.
     *  
     * @param  ArrayObject  $b
     * @return PageObjectType
     */
    public function setB(ArrayObject $b): PageObjectType
    {
        $this->setEntry('B', $b);
        return $this;
    }

    /**
     * Set maximum display duration (in seconds).
     *  
     * @param  float  $dur
     * @return PageObjectType
     */
    public function setDur(float $dur): PageObjectType
    {
        $value = Factory::create('Papier\Type\RealType', $dur);

        $this->setEntry('Dur', $value);
        return $this;
    }

    /**
     * Set transition effect.
     *  
     * @param DictionaryObject $trans
     * @return PageObjectType
     */
    public function setTrans(DictionaryObject $trans): PageObjectType
    {
        $this->setEntry('Trans', $trans);
        return $this;
    }

    /**
     * Set annotations.
     *  
     * @param  ArrayObject  $annots
     * @return PageObjectType
     */
    public function setAnnots(ArrayObject $annots): PageObjectType
    {
        $this->setEntry('Annots', $annots);
        return $this;
    }

    /**
     * Set additional actions.
     *  
     * @param DictionaryObject $aa
     * @return PageObjectType
     */
    public function setAA(DictionaryObject $aa): PageObjectType
    {
        $this->setEntry('AA', $aa);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param  StreamObject  $metadata
     * @return PageObjectType
     */
    public function setMetadata(StreamObject $metadata): PageObjectType
    {
        $this->setEntry('Metadata', $metadata);
        return $this;
    }

    /**
     * Set page-piece dictionary.
     *  
     * @param  DictionaryObject $pieceinfo
     * @return PageObjectType
     */
    public function setPieceInfo(DictionaryObject $pieceinfo): PageObjectType
    {
        $this->setEntry('PieceInfo', $pieceinfo);
        return $this;
    }

    /**
     * Set integer key of this page in structural parent tree.
     *  
     * @param int $struct
     * @return PageObjectType
     */
    public function setStructParents(int $struct): PageObjectType
    {
        $value = Factory::create('Papier\Type\IntegerType', $struct);

        $this->setEntry('StructParents', $value);
        return $this;
    }

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param  ByteStringType  $id
     * @return PageObjectType
     */
    public function setID(ByteStringType $id): PageObjectType
    {
        $this->setEntry('ID', $id);
        return $this;
    }

    /**
     * Set preferred zoom.
     *  
     * @param  float  $pz
     * @return PageObjectType
     */
    public function setPZ(float $pz): PageObjectType
    {
        $value = Factory::create('Papier\Type\RealType', $pz);

        $this->setEntry('PZ', $value);
        return $this;
    }

    /**
     * Set colour separations.
     *  
     * @param  DictionaryObject $separationinfo
     * @return PageObjectType
     */
    public function setSeparationInfo(DictionaryObject $separationinfo): PageObjectType
    {
        $this->setEntry('SeparationInfo', $separationinfo);
        return $this;
    }

    /**
     * Set tab order.
     *  
     * @param string $tabs
     * @return PageObjectType
     * @throws InvalidArgumentException if the provided argument is not a valid tab order.
     */
    public function setTabs(string $tabs): PageObjectType
    {
        if (!TabOrderValidator::isValid($tabs)) {
            throw new InvalidArgumentException("Tabs is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $tabs);
        $this->setEntry('Tabs', $value);
        return $this;
    }

    /**
     * Set name of the originating page object.
     *  
     * @param  DictionaryObject  $template
     * @return PageObjectType
     */
    public function setTemplateInstantiated(DictionaryObject $template): PageObjectType
    {
        $this->setEntry('TemplateInstantiated', $template);
        return $this;
    }

    /**
     * Set navigation node dictionary.
     *  
     * @param  DictionaryObject $pressteps
     * @return PageObjectType
     */
    public function setPresSteps(DictionaryObject $pressteps): PageObjectType
    {
        $this->setEntry('PresSteps', $pressteps);
        return $this;
    }

    /**
     * Set default user space units (in multiple of 1/72 inch).
     *  
     * @param  float  $userunit
     * @return PageObjectType
     */
    public function setUserUnit(float $userunit): PageObjectType
    {
        $value = Factory::create('Papier\Type\RealType', $userunit);

        $this->setEntry('UserUnit', $value);
        return $this;
    }

     /**
     * Set viewport dictionaries.
     *  
     * @param DictionaryObject $vp
      * @return PageObjectType
      *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setVP(DictionaryObject $vp): PageObjectType
    {
        $this->setEntry('VP', $vp);
        return $this;
    }

    /**
     * Format page object's content.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\NameType', 'Page');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}