<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\BaseObject;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\BooleanType;
use Papier\Type\Base\IntegerType;
use Papier\Type\Base\StreamType;
use Papier\Validator\BitsPerComponentValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\RenderingIntentValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class ImageStreamType extends StreamType
{
 
    /**
     * Set width.
     *  
     * @param  int  $width
     * @return ImageStreamType
     */
    public function setWidth(int $width): ImageStreamType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $width);

        $this->setEntry('Width', $value);
        return $this;
    } 

    /**
     * Set height.
     *  
     * @param  int  $height
     * @return ImageStreamType
     */
    public function setHeight(int $height): ImageStreamType
    {

        $value = Factory::create('Papier\Type\Base\IntegerType', $height);

        $this->setEntry('Height', $value);
        return $this;
    } 

    /**
     * Set color space.
     *  
     * @param  mixed  $space
     * @return ImageStreamType
     *@throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     */
    public function setColorSpace($space): ImageStreamType
	{
        if (!StringValidator::isValid($space) && !$space instanceof ArrayObject) {
            throw new InvalidArgumentException("ColorSpace is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $space instanceof ArrayObject ? $space : Factory::create('Papier\Type\Base\NameType', $space);

        $this->setEntry('ColorSpace', $value);
        return $this;
    } 

     /**
     * Set the number of bits used to represent each colour component.
     *  
     * @param int $bits
     * @return ImageStreamType
	  * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setBitsPerComponent(int $bits): ImageStreamType
	{
        if (!BitsPerComponentValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerComponent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $bits);

        $this->setEntry('BitsPerComponent', $value);
        return $this;
    }

    /**
     * Set name of rendering intent.
     *  
     * @param string $intent
     * @return ImageStreamType
	 * @throws InvalidArgumentException if the provided argument is not a valid rendering intent.
     */
    public function setIntent(string $intent): ImageStreamType
	{
        if (!RenderingIntentValidator::isValid($intent)) {
            throw new InvalidArgumentException("Intent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\NameType', $intent);
        $this->setEntry('Intent', $value);
        return $this;
    }

    /**
     * Set image mask.
     *  
     * @param bool $imageMask
     * @return ImageStreamType
	 * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     */
    public function setImageMask(bool $imageMask): ImageStreamType
	{
        $value = Factory::create('Papier\Type\Base\BooleanType', $imageMask);
        $this->setEntry('ImageMask', $value);
        return $this;
    }

    /**
     * Set mask.
     *  
     * @param  mixed  $mask
     * @return ImageStreamType
     *@throws InvalidArgumentException if the provided argument is not of type 'StreamObject' or 'ArrayObject'.
	 */
    public function setMask($mask): ImageStreamType
	{
        if (!$mask instanceof StreamObject && !$mask instanceof ArrayObject) {
            throw new InvalidArgumentException("Mask is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Mask', $mask);
        return $this;
    }

    /**
     * Set map from image samples to the appropriate ranges of values.
     *  
     * @param array<float> $decode
     * @return ImageStreamType
	 * @throws InvalidArgumentException if the provided argument is not of type 'array' and if each element of the provided argument is not of type 'int' or 'float.
     */
    public function setDecode(array $decode): ImageStreamType
	{
        if (!NumbersArrayValidator::isValid($decode)) {
            throw new InvalidArgumentException("Decode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumbersArrayType', $decode);

        $this->setEntry('Decode', $value);
        return $this;
    }

    /**
     * Set interpolation.
     *  
     * @param bool $interpolate
     * @return ImageStreamType
	 */
    public function setInterpolate(bool $interpolate = true): ImageStreamType
	{
        $value = Factory::create('Papier\Type\Base\BooleanType', $interpolate);

        $this->setEntry('Interpolate', $value);
        return $this;
    }
    
    /**
     * Set alternates.
     *  
     * @param ArrayObject $alternates
     * @return ImageStreamType
	 */
    public function setAlternates(ArrayObject $alternates): ImageStreamType
	{
        $this->setEntry('Alternates', $alternates);
        return $this;
    }

    /**
     * Set soft-mask image.
     *  
     * @param StreamObject $smask
     * @return ImageStreamType
	 */
    public function setSMask(StreamObject $smask): ImageStreamType
	{
        $this->setEntry('SMask', $smask);
        return $this;
    }

    /**
     * Set soft-mask image.
     *  
     * @param int $indata
     * @return ImageStreamType
	 */
    public function setSMaskInData(int $indata): ImageStreamType
	{
        $value = Factory::create('Papier\Type\Base\IntegerType', $indata);

        $this->setEntry('SMaskInData', $value);
        return $this;
    }

    /**
     * Set name.
     *  
     * @param  string  $name
     * @return ImageStreamType
	 */
    public function setName(string $name): ImageStreamType
	{
        $value = Factory::create('Papier\Type\Base\NameType', $name);

        $this->setEntry('Papier\Type\Base\NameType', $value);
        return $this;
    }

    /**
     * Set integer key of image's entry in the structural parent tree.
     *  
     * @param int $struct
     * @return ImageStreamType
	 */
    public function setStructParent(int $struct): ImageStreamType
	{
        $value = Factory::create('Papier\Type\Base\IntegerType', $struct);

        $this->setEntry('StructParent', $value);
        return $this;
    }

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param ByteStringType $id
     * @return ImageStreamType
	 */
    public function setID(ByteStringType $id): ImageStreamType
	{
        $this->setEntry('ID', $id);
        return $this;
    }

    /**
     * Set open prepress interface.
     *  
     * @param  DictionaryObject  $opi
     * @return ImageStreamType
	 */
    public function setOPI(DictionaryObject $opi): ImageStreamType
	{
        $this->setEntry('OPI', $opi);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param StreamObject $metadata
     * @return ImageStreamType
	 */
    public function setMetadata(StreamObject $metadata): ImageStreamType
	{
       $this->setEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set optional content.
     *  
     * @param DictionaryObject $oc
     * @return ImageStreamType
	 */
    public function setOC(DictionaryObject $oc): ImageStreamType
	{
        $this->setEntry('OC', $oc);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\Base\NameType', 'XObject');
        $this->setEntry('Type', $type);

        $subtype = Factory::create('Papier\Type\Base\NameType', 'Image');
        $this->setEntry('Subtype', $subtype);

        if (!$this->hasEntry('Width')) {
            throw new RuntimeException("Width is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Height')) {
            throw new RuntimeException("Height is missing. See ".__CLASS__." class's documentation for possible values.");
        }

		if ($this->hasEntry('ColorSpace')) {
			/** @var BaseObject $colorSpace */
			$colorSpace = $this->getEntry('ColorSpace');

			if ($colorSpace->getValue() == 'Pattern') {
				throw new RuntimeException("ColorSpace is incompatible. See ".__CLASS__." class's documentation for possible values.");
			}
		}


		if ($this->hasEntry('ImageMask')) {
			/** @var BooleanType $imageMask */
			$imageMask = $this->getEntry('ImageMask');

			if ($imageMask->isTrue() && $this->hasEntry('Mask')) {
				throw new RuntimeException("Mask is not allowed. See ".__CLASS__." class's documentation for possible values.");
			}

			if ($imageMask->isTrue() && $this->hasEntry('BitsPerComponent')) {
				/** @var IntegerType $bitsPerComponent */
				$bitsPerComponent = $this->getEntry('BitsPerComponent');
				if ($bitsPerComponent->getValue() == 1) {
					throw new RuntimeException("BitsPerComponent is incompatible. See ".__CLASS__." class's documentation for possible values.");
				}
			}
		}

        /*
        if ($this->hasEntry('Filter') && $this->getEntry('Filter')->has(FilterType::JPX_DECODE)) {
            $this->unsetEntry('BitsPerComponent');
        }

        if ($this->hasEntry('Filter') && ($this->getEntry('Filter')->has(FilterType::CCITT_FAX_DECODE) || $this->getEntry('Filter')->has(FilterType::JBIG2_DECODE)) && $this->getEntry('BitsPerComponent')->getValue() == 1) {
            throw new RuntimeException("BitsPerComponent is incompatible. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Filter') && ($this->getEntry('Filter')->has(FilterType::RUN_LENGTH_DECODE) || $this->getEntry('Filter')->has(FilterType::DCT_DECODE)) && $this->getEntry('BitsPerComponent')->getValue() == 8) {
            throw new RuntimeException("BitsPerComponent is incompatible. See ".__CLASS__." class's documentation for possible values.");
        }
        */

        return parent::format();
    }
}