<?php

namespace Papier\Type;

use Papier\Object\StreamObject;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;
use Papier\Object\NameObject;

use Papier\Validator\IntegerValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\StringValidator;

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

        return parent::format();
    }
}