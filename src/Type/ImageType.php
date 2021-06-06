<?php

namespace Papier\Type;

use Papier\Object\StreamObject;
use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;

use Papier\Validator\StringValidator;
use Papier\Validator\BitsPerComponentValidator;
use Papier\Validator\RenderingIntentValidator;
use Papier\Validator\NumbersArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class ImageType extends StreamObject
{
 
    /**
     * Set width.
     *  
     * @param  int  $width
     * @return ImageType
     */
    public function setWidth(int $width): ImageType
    {
        $value = Factory::create('Integer', $width);

        $this->setEntry('Width', $value);
        return $this;
    } 

    /**
     * Set height.
     *  
     * @param  int  $height
     * @return ImageType
     */
    public function setHeight(int $height): ImageType
    {

        $value = Factory::create('Integer', $height);

        $this->setEntry('Height', $value);
        return $this;
    } 

    /**
     * Set color space.
     *  
     * @param  mixed  $space
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return ImageType
     */
    public function setColorSpace($space): ImageType
    {
        if (!StringValidator::isValid($space) && !$space instanceof ArrayObject) {
            throw new InvalidArgumentException("ColorSpace is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $space instanceof ArrayObject ? $space : Factory::create('Name', $space);

        $this->setEntry('ColorSpace', $value);
        return $this;
    } 

     /**
     * Set the number of bits used to represent each colour component.
     *  
     * @param int $bits
     * @return ImageType
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setBitsPerComponent(int $bits): ImageType
    {
        if (!BitsPerComponentValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerComponent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $bits);

        $this->setEntry('BitsPerComponent', $value);
        return $this;
    }

    /**
     * Set name of rendering intent.
     *  
     * @param string $intent
     * @return ImageType
     * @throws InvalidArgumentException if the provided argument is not a valid rendering intent.
     */
    public function setIntent(string $intent): ImageType
    {
        if (!RenderingIntentValidator::isValid($intent)) {
            throw new InvalidArgumentException("Intent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $intent);
        $this->setEntry('Intent', $value);
        return $this;
    }

    /**
     * Set image mask.
     *  
     * @param bool $imagemask
     * @return ImageType
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     */
    public function setImageMask(bool $imagemask): ImageType
    {
        $value = Factory::create('Boolean', $imagemask);
        $this->setEntry('ImageMask', $value);
        return $this;
    }

    /**
     * Set mask.
     *  
     * @param  mixed  $mask
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject' or 'ArrayObject'.
     * @return ImageType
     */
    public function setMask($mask): ImageType
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
     * @param array $decode
     * @return ImageType
     * @throws InvalidArgumentException if the provided argument is not of type 'array' and if each element of the provided argument is not of type 'int' or 'float.
     */
    public function setDecode(array $decode): ImageType
    {
        if (!NumbersArrayValidator::isValid($decode)) {
            throw new InvalidArgumentException("Decode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $decode);

        $this->setEntry('Decode', $value);
        return $this;
    }

    /**
     * Set interpolation.
     *  
     * @param bool $interpolate
     * @return ImageType
     */
    public function setInterpolate(bool $interpolate = true): ImageType
    {
        $value = Factory::create('Boolean', $interpolate);

        $this->setEntry('Interpolate', $value);
        return $this;
    }
    
    /**
     * Set alternates.
     *  
     * @param ArrayObject $alternates
     * @return ImageType
     */
    public function setAlternates(ArrayObject $alternates): ImageType
    {
        $this->setEntry('Alternates', $alternates);
        return $this;
    }

    /**
     * Set soft-mask image.
     *  
     * @param StreamObject $smask
     * @return ImageType
     */
    public function setSMask(StreamObject $smask): ImageType
    {
        $this->setEntry('SMask', $smask);
        return $this;
    }

    /**
     * Set soft-mask image.
     *  
     * @param int $indata
     * @return ImageType
     */
    public function setSMaskInData(int $indata): ImageType
    {
        $value = Factory::create('Integer', $indata);

        $this->setEntry('SMaskInData', $value);
        return $this;
    }

    /**
     * Set name.
     *  
     * @param  string  $name
     * @return ImageType
     */
    public function setName(string $name): ImageType
    {
        $value = Factory::create('Name', $name);

        $this->setEntry('Name', $value);
        return $this;
    }

    /**
     * Set integer key of image's entry in the structural parent tree.
     *  
     * @param int $struct
     * @return ImageType
     */
    public function setStructParent(int $struct): ImageType
    {
        $value = Factory::create('Integer', $struct);

        $this->setEntry('StructParent', $value);
        return $this;
    }

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param ByteStringType $id
     * @return ImageType
     */
    public function setID(ByteStringType $id): ImageType
    {
        $this->setEntry('ID', $id);
        return $this;
    }

    /**
     * Set open prepress interface.
     *  
     * @param  DictionaryObject  $opi
     * @return ImageType
     */
    public function setOPI(DictionaryObject $opi): ImageType
    {
        $this->setEntry('OPI', $opi);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param StreamObject $metadata
     * @return ImageType
     */
    public function setMetadata(StreamObject $metadata): ImageType
    {
       $this->setEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set optional content.
     *  
     * @param DictionaryObject $oc
     * @return ImageType
     */
    public function setOC(DictionaryObject $oc): ImageType
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
        $type = Factory::create('Name', 'XObject');
        $this->setEntry('Type', $type);

        $subtype = Factory::create('Name', 'Image');
        $this->setEntry('Subtype', $subtype);

        if (!$this->hasEntry('Width')) {
            throw new RuntimeException("Width is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Height')) {
            throw new RuntimeException("Height is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('ColorSpace') && $this->getEntry('ColorSpace')->getValue() == 'Pattern') {
            throw new RuntimeException("ColorSpace is incompatible. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('ImageMask') && $this->getEntry('ImageMask')->isTrue() && $this->hasEntry('Mask')) {
            throw new RuntimeException("Mask is not allowed. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('ImageMask') && $this->getEntry('ImageMask')->isTrue() && $this->hasEntry('BitsPerComponent') && $this->getEntry('BitsPerComponent')->getValue() == 1) {
            throw new RuntimeException("BitsPerComponent is incompatible. See ".__CLASS__." class's documentation for possible values.");
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