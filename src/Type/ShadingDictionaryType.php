<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\ShadingTypeValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\BooleanValidator;

use InvalidArgumentException;
use RuntimeException;

class ShadingDictionaryType extends DictionaryObject
{
    /**
     * Set shading type.
     *  
     * @param int $type
     * @return ShadingDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     */
    public function setShadingType(int $type): ShadingDictionaryType
    {
        if (!ShadingTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("ShadingType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $type);

        $this->setEntry('ShadingType', $value);
        return $this;
    } 

    /**
     * Set colour space.
     *  
     * @param  mixed $space
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return ShadingDictionaryType
     */
    public function setColourSpace($space): ShadingDictionaryType
    {
        if (!StringValidator::isValid($space) && (!$space instanceof ArrayObject)) {
            throw new InvalidArgumentException("ColorSpace is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $space instanceof ArrayObject ? $space : Factory::create('Papier\Type\NameType', $space);

        $this->setEntry('ColorSpace', $value);
        return $this;
    } 

    /**
     * Set background.
     *  
     * @param ArrayObject $background
     * @return ShadingDictionaryType
     */
    public function setBackground(ArrayObject $background): ShadingDictionaryType
    {
        $this->setEntry('Background', $background);
        return $this;
    }

    /**
     * Set boundaries of the shading's bounding box.
     *  
     * @param  RectangleType  $bbox
     * @return ShadingDictionaryType
     */
    public function setBBox(RectangleType $bbox): ShadingDictionaryType
    {
        $this->setEntry('BBox', $bbox);
        return $this;
    }

    /**
     * Set a flag to filter the shading function to prevent aliasing artifacts.
     *  
     * @param bool $antialias
     * @return ShadingDictionaryType
     */
    public function setAntiAlias(bool $antialias): ShadingDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $antialias);

        $this->setEntry('AntiAlias', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
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