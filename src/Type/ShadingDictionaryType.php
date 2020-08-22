<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\ShadingTypeValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\BooleanValidator;

use InvalidArgumentException;

class ShadingDictionaryType extends DictionaryObject
{
    /**
     * Set shading type.
     *  
     * @param  int $type
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     * @return \Papier\Type\ShadingDictionaryType
     */
    public function setShadingType($type)
    {
        if (!ShadingTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("ShadingType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $type);

        $this->setEntry('ShadingType', $value);
        return $this;
    } 

    /**
     * Set colour space.
     *  
     * @param  mixed $space
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return \Papier\Type\ShadingDictionaryType
     */
    public function setColourSpace($space)
    {
        if (!StringValidator::isValid($space) && (!$space instanceof ArrayObject)) {
            throw new InvalidArgumentException("ColorSpace is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $space instanceof ArrayObject ? $space : Factory::create('Name', $space);

        $this->setEntry('ColorSpace', $value);
        return $this;
    } 

    /**
     * Set background.
     *  
     * @param  \Papier\Object\ArrayObject $background
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\ShadingDictionaryType
     */
    public function setBackground($background)
    {
        if (!$background instanceof ArrayObject) {
            throw new InvalidArgumentException("Background is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Background', $background);
        return $this;
    }

    /**
     * Set boundaries of the shading's bounding box.
     *  
     * @param  \Papier\Type\RectangleType  $bbox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\ShadingDictionaryType
     */
    public function setBBox($bbox)
    {
        if (!$bbox instanceof RectangleType) {
            throw new InvalidArgumentException("BBox is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('BBox', $bbox);
        return $this;
    }

    /**
     * Set a flag to filter the shading function to prevent aliasing artifacts.
     *  
     * @param  bool $antialias
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ShadingDictionaryType
     */
    public function setAntiAlias($antialias)
    {
        if (!BooleanValidator::isValid($antialias)) {
            throw new InvalidArgumentException("AntiAlias is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $antialias);

        $this->setEntry('AntiAlias', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        if (!$this->hasEntry('ShadingType')) {
            throw new RuntimeException("ShadingType is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('ColorSpace')) {
            throw new RuntimeException("ColorSpace is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}