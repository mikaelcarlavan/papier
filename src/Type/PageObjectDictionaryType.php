<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\DateType;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\TabOrderValidator;


class PageObjectDictionaryType extends DictionaryType
{
    /**
     * Set parent.
     *  
     * @param PageTreeNodeDictionaryType $parent
     * @return PageObjectDictionaryType
     */
    public function setParent(PageTreeNodeDictionaryType $parent): PageObjectDictionaryType
    {
        $this->setEntry('Parent', $parent);
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return PageObjectDictionaryType
      */
    public function getParent(): PageObjectDictionaryType
    {
		/** @var PageObjectDictionaryType $parent */
		$parent = $this->getEntry('Parent');
        return $parent;
    } 

    /**
     * Set date and time of last object's modification.
     *  
     * @param DateType $date
     * @return PageObjectDictionaryType
     */
    public function setLastModified(DateType $date): PageObjectDictionaryType
    {
        $this->setEntry('LastModified', $date);
        return $this;
    } 

    /**
     * Set resources.
     *  
     * @param DictionaryObject $resources
     * @return PageObjectDictionaryType
     */
    public function setResources(DictionaryObject $resources): PageObjectDictionaryType
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
     * @return PageObjectDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setMediaBox(RectangleNumbersArrayType $mediabox): PageObjectDictionaryType
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
            $mediaBox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setMediaBox($mediaBox);
        }

		/** @var RectangleNumbersArrayType $mediaBox */
		$mediaBox = $this->getEntry('MediaBox');
        return $mediaBox;
    }

    /**
     * Set the visible region of default user space.
     *  
     * @param RectangleNumbersArrayType $cropbox
     * @return PageObjectDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setCropBox(RectangleNumbersArrayType $cropbox): PageObjectDictionaryType
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
			$cropBox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setCropBox($cropBox);
        }

		/** @var RectangleNumbersArrayType $cropBox */
		$cropBox = $this->getEntry('CropBox');
        return $cropBox;
    }

    /**
     * Set region to which the contents of the page shall be clipped when output in a production enviroment.
     *  
     * @param RectangleNumbersArrayType $bleedbox
     * @return PageObjectDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setBleedBox(RectangleNumbersArrayType $bleedbox): PageObjectDictionaryType
    {
        $this->setEntry('BleedBox', $bleedbox);
        return $this;
    }

    /**
     * Get bleedbox.
     *  
     * @return RectangleNumbersArrayType
     */
    public function getBleedBox(): RectangleNumbersArrayType
    {
        if (!$this->hasEntry('BleedBox')) {
            $bleedBox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setBleedBox($bleedBox);
        }

		/** @var RectangleNumbersArrayType $bleedBox */
		$bleedBox = $this->getEntry('BleedBox');
        return $bleedBox;
    }

    /**
     * Set intended dimensions of the finished page after trimming.
     *  
     * @param RectangleNumbersArrayType $trimbox
     * @return PageObjectDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setTrimBox(RectangleNumbersArrayType $trimbox): PageObjectDictionaryType
    {
        $this->setEntry('TrimBox', $trimbox);
        return $this;
    }

    /**
     * Get trimbox.
     *  
     * @return RectangleNumbersArrayType
     */
    public function getTrimBox(): RectangleNumbersArrayType
    {
        if (!$this->hasEntry('TrimBox')) {
            $trimBox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setTrimBox($trimBox);
        }

		/** @var RectangleNumbersArrayType $trimBox */
		$trimBox = $this->getEntry('TrimBox');
        return $trimBox;
    }

    /**
     * Set extend of the page's meaningful content.
     *  
     * @param RectangleNumbersArrayType $artbox
     * @return PageObjectDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setArtBox(RectangleNumbersArrayType $artbox): PageObjectDictionaryType
    {
        $this->setEntry('ArtBox', $artbox);
        return $this;
    }

    /**
     * Get artbox.
     *  
     * @return RectangleNumbersArrayType
     */
    public function getArtBox(): RectangleNumbersArrayType
    {
        if (!$this->hasEntry('ArtBox')) {
            $artBox = Factory::create('Papier\Type\RectangleNumbersArrayType');
            $this->setArtBox($artBox);
        }

		/** @var RectangleNumbersArrayType $artBox */
		$artBox = $this->getEntry('ArtBox');
        return $artBox;
    }

    /**
     * Set colours and other visual characteristics.
     *  
     * @param DictionaryObject $boxcolorinfo
     * @return PageObjectDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setBoxColorInfo(DictionaryObject $boxcolorinfo): PageObjectDictionaryType
    {
        $this->setEntry('BoxColorInfo', $boxcolorinfo);
        return $this;
    }


    /**
     * Set the contents of the page.
     *  
     * @param  mixed  $contents
     * @return PageObjectDictionaryType
     *@throws InvalidArgumentException if the provided argument is not of type 'StreamObject' or 'ArrayObject'.
     */
    public function setContents($contents): PageObjectDictionaryType
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

		/** @var ContentStreamType $contents */
		$contents = $this->getEntry('Contents');
        return $contents;
    }

    /**
     * Set the number of degrees by which the page should be rotated before printed or displayed.
     *  
     * @param int $rotate
     * @return PageObjectDictionaryType
	 */
    public function setRotate(int $rotate): PageObjectDictionaryType
	{
        $value = Factory::create('Papier\Type\Base\IntegerType', $rotate);

        $this->setEntry('Rotate', $value);
        return $this;
    }

    /**
     * Set attributes of the page's page group for use in the transparent imaging model.
     *  
     * @param DictionaryObject $group
     * @return PageObjectDictionaryType
	 */
    public function setGroup(DictionaryObject $group): PageObjectDictionaryType
	{
        $this->setEntry('Group', $group);
        return $this;
    }


    /**
     * Set the thumbnail image of the page.
     *  
     * @param  StreamObject  $thumb
     * @return PageObjectDictionaryType
	 */
    public function setThumb(StreamObject $thumb): PageObjectDictionaryType
	{
        $this->setEntry('Thumb', $thumb);
        return $this;
    }

    /**
     * Set references to articles beads.
     *  
     * @param  ArrayObject  $b
     * @return PageObjectDictionaryType
	 */
    public function setB(ArrayObject $b): PageObjectDictionaryType
	{
        $this->setEntry('B', $b);
        return $this;
    }

    /**
     * Set maximum display duration (in seconds).
     *  
     * @param  float  $dur
     * @return PageObjectDictionaryType
	 */
    public function setDur(float $dur): PageObjectDictionaryType
	{
        $value = Factory::create('Papier\Type\Base\RealType', $dur);

        $this->setEntry('Dur', $value);
        return $this;
    }

    /**
     * Set transition effect.
     *  
     * @param DictionaryObject $trans
     * @return PageObjectDictionaryType
	 */
    public function setTrans(DictionaryObject $trans): PageObjectDictionaryType
	{
        $this->setEntry('Trans', $trans);
        return $this;
    }

    /**
     * Set annotations.
     *  
     * @param  ArrayObject  $annots
     * @return PageObjectDictionaryType
	 */
    public function setAnnots(ArrayObject $annots): PageObjectDictionaryType
	{
        $this->setEntry('Annots', $annots);
        return $this;
    }

    /**
     * Set additional actions.
     *  
     * @param DictionaryObject $aa
     * @return PageObjectDictionaryType
	 */
    public function setAA(DictionaryObject $aa): PageObjectDictionaryType
	{
        $this->setEntry('AA', $aa);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param  StreamObject  $metadata
     * @return PageObjectDictionaryType
	 */
    public function setMetadata(StreamObject $metadata): PageObjectDictionaryType
	{
        $this->setEntry('Metadata', $metadata);
        return $this;
    }

    /**
     * Set page-piece dictionary.
     *  
     * @param  DictionaryObject $pieceinfo
     * @return PageObjectDictionaryType
	 */
    public function setPieceInfo(DictionaryObject $pieceinfo): PageObjectDictionaryType
	{
        $this->setEntry('PieceInfo', $pieceinfo);
        return $this;
    }

    /**
     * Set integer key of this page in structural parent tree.
     *  
     * @param int $struct
     * @return PageObjectDictionaryType
	 */
    public function setStructParents(int $struct): PageObjectDictionaryType
	{
        $value = Factory::create('Papier\Type\Base\IntegerType', $struct);

        $this->setEntry('StructParents', $value);
        return $this;
    }

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param  ByteStringType  $id
     * @return PageObjectDictionaryType
	 */
    public function setID(ByteStringType $id): PageObjectDictionaryType
	{
        $this->setEntry('ID', $id);
        return $this;
    }

    /**
     * Set preferred zoom.
     *  
     * @param  float  $pz
     * @return PageObjectDictionaryType
	 */
    public function setPZ(float $pz): PageObjectDictionaryType
	{
        $value = Factory::create('Papier\Type\Base\RealType', $pz);

        $this->setEntry('PZ', $value);
        return $this;
    }

    /**
     * Set colour separations.
     *  
     * @param  DictionaryObject $separationinfo
     * @return PageObjectDictionaryType
	 */
    public function setSeparationInfo(DictionaryObject $separationinfo): PageObjectDictionaryType
	{
        $this->setEntry('SeparationInfo', $separationinfo);
        return $this;
    }

    /**
     * Set tab order.
     *  
     * @param string $tabs
     * @return PageObjectDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not a valid tab order.
     */
    public function setTabs(string $tabs): PageObjectDictionaryType
	{
        if (!TabOrderValidator::isValid($tabs)) {
            throw new InvalidArgumentException("Tabs is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\NameType', $tabs);
        $this->setEntry('Tabs', $value);
        return $this;
    }

    /**
     * Set name of the originating page object.
     *  
     * @param  DictionaryObject  $template
     * @return PageObjectDictionaryType
	 */
    public function setTemplateInstantiated(DictionaryObject $template): PageObjectDictionaryType
	{
        $this->setEntry('TemplateInstantiated', $template);
        return $this;
    }

    /**
     * Set navigation node dictionary.
     *  
     * @param  DictionaryObject $pressteps
     * @return PageObjectDictionaryType
	 */
    public function setPresSteps(DictionaryObject $pressteps): PageObjectDictionaryType
	{
        $this->setEntry('PresSteps', $pressteps);
        return $this;
    }

    /**
     * Set default user space units (in multiple of 1/72 inch).
     *  
     * @param  float  $userunit
     * @return PageObjectDictionaryType
	 */
    public function setUserUnit(float $userunit): PageObjectDictionaryType
	{
        $value = Factory::create('Papier\Type\Base\RealType', $userunit);

        $this->setEntry('UserUnit', $value);
        return $this;
    }

     /**
     * Set viewport dictionaries.
     *  
     * @param DictionaryObject $vp
      * @return PageObjectDictionaryType
	  *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setVP(DictionaryObject $vp): PageObjectDictionaryType
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
        $type = Factory::create('Papier\Type\Base\NameType', 'Page');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}