<?php

namespace Papier\Type;

use Papier\Filter\FilterType;

use Papier\Object\StreamObject;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;
use Papier\Object\NameObject;

use Papier\Validator\IntegerValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\BitsPerComponentValidator;
use Papier\Validator\RenderingIntentValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class ImageType extends StreamObject
{
 
    /**
     * Set width.
     *  
     * @param  int  $width
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\ImageType
     */
    public function setWidth($width)
    {
        if (!IntegerValidator::isValid($width)) {
            throw new InvalidArgumentException("Width is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $width);

        $this->setEntry('Width', $value);
        return $this;
    } 

    /**
     * Set height.
     *  
     * @param  int  $height
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\ImageType
     */
    public function setHeight($height)
    {
        if (!IntegerValidator::isValid($height)) {
            throw new InvalidArgumentException("Height is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $height);

        $this->setEntry('Height', $value);
        return $this;
    } 

    /**
     * Set color space.
     *  
     * @param  mixed  $space
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return \Papier\Type\ImageType
     */
    public function setColorSpace($space)
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
     * @param  array  $bits
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\ImageType
     */
    public function setBitsPerComponent($bits)
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
     * @param  string  $intent
     * @throws InvalidArgumentException if the provided argument is not a valid rendering intent.
     * @return \Papier\Type\ImageType
     */
    public function setIntent($intent)
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
     * @param  bool  $imagemask
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ImageType
     */
    public function setImageMask($imagemask)
    {
        if (!BooleanValidator::isValid($imagemask)) {
            throw new InvalidArgumentException("ImageMask is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $imagemask);
        $this->setEntry('ImageMask', $value);
        return $this;
    }

    /**
     * Set mask.
     *  
     * @param  mixed  $mask
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject' or 'ArrayObject'.
     * @return \Papier\Type\ImageType
     */
    public function setMask($mask)
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
     * @param  array  $decode
     * @throws InvalidArgumentException if the provided argument is not of type 'array' and if each element of the provided argument is not of type 'int' or 'float.
     * @return \Papier\Type\ImageType
     */
    public function setDecode($decode)
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
     * @param  bool  $interpolate
     * @throws InvalidArgumentException if the provided argument does not inherit 'bool'.
     * @return \Papier\Type\ImageType
     */
    public function setInterpolate($interpolate = true)
    {
        if (!BooleanValidator::isValid($interpolate)) {
            throw new InvalidArgumentException("Interpolate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $interpolate);

        $this->setEntry('Interpolate', $value);
        return $this;
    }
    
    /**
     * Set alternates.
     *  
     * @param  \Papier\Object\ArrayObject  $alternates
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\ImageType
     */
    public function setAlternates($alternates)
    {
        if (!$alternates instanceof ArrayObject) {
            throw new InvalidArgumentException("Alternates is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Alternates', $alternates);
        return $this;
    }

    /**
     * Set soft-mask image.
     *  
     * @param  \Papier\Object\StreamObject  $smask
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\ImageType
     */
    public function setSMask($smask)
    {
        if (!$smask instanceof StreamObject) {
            throw new InvalidArgumentException("SMask is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('SMask', $smask);
        return $this;
    }

    /**
     * Set soft-mask image.
     *  
     * @param  int  $indata
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\ImageType
     */
    public function setSMaskInData($indata)
    {
        if (!IntegerValidator::isValid($indata)) {
            throw new InvalidArgumentException("SMaskInData is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $indata);

        $this->setEntry('SMaskInData', $value);
        return $this;
    }

    /**
     * Set name.
     *  
     * @param  mixed  $name
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\ImageType
     */
    public function setName($name)
    {
        if (!StringValidator::isValid($space)) {
            throw new InvalidArgumentException("Name is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $name);

        $this->setEntry('Name', $value);
        return $this;
    }

    /**
     * Set integer key of image's entry in the structural parent tree.
     *  
     * @param  int  $struct
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\ImageType
     */
    public function setStructParent($struct)
    {
        if (!IntegerValidator::isValid($struct)) {
            throw new InvalidArgumentException("StructParent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $struct);

        $this->setEntry('StructParent', $value);
        return $this;
    }

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param  \Papier\Type\ByteStringType  $id
     * @throws InvalidArgumentException if the provided argument is not of type 'ByteStringType'.
     * @return \Papier\Type\ImageType
     */
    public function setID($id)
    {
        if (!$id instanceof ByteStringType) {
            throw new InvalidArgumentException("ID is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ID', $id);
        return $this;
    }

    /**
     * Set open prepress interface.
     *  
     * @param  \Papier\Object\DictionaryObject  $opi
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\ImageType
     */
    public function setOPI($opi)
    {
        if (!$opi instanceof DictionaryObject) {
            throw new InvalidArgumentException("OPI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('OPI', $opi);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param  \Papier\Object\StreamObject  $metadata
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\ImageType
     */
    public function setMetadata($metadata)
    {
        if (!$metadata instanceof StreamObject) {
            throw new InvalidArgumentException("Metadata is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set optional content.
     *  
     * @param  \Papier\Object\DictionaryObject  $oc
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\ImageType
     */
    public function setOC($oc)
    {
        if (!$oc instanceof DictionaryObject) {
            throw new InvalidArgumentException("OC is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('OC', $oc);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
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