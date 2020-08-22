<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;

use Papier\Type\PatternDictionaryType;

use Papier\Factory\Factory;
use Papier\Validator\PaintTypeValidator;
use Papier\Validator\TilingTypeValidator;
use Papier\Validator\NumbersArrayValidator;

use Papier\Graphics\PatternType;


use InvalidArgumentException;
use RuntimeException;

class TilingPatternDictionaryType extends PatternDictionaryType
{
    /**
     * Set paint type.
     *  
     * @param  int $type
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     * @return \Papier\Type\TilingPatternDictionaryType
     */
    public function setPaintType($type)
    {
        if (!PaintTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("PaintType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $type);

        $this->setEntry('PaintType', $value);
        return $this;
    } 

    /**
     * Set tiling type.
     *  
     * @param  int $type
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     * @return \Papier\Type\TilingPatternDictionaryType
     */
    public function setTilingType($type)
    {
        if (!TilingTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("TilingType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $type);

        $this->setEntry('TilingType', $value);
        return $this;
    }

    /**
     * Set boundaries of the pattern's cell bounding box.
     *  
     * @param  \Papier\Type\RectangleType  $bbox
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     * @return \Papier\Type\TilingPatternDictionaryType
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
     * Set horizontal spacing between patteern cells.
     *  
     * @param  mixed  $xstep
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Type\TilingPatternDictionaryType
     */
    public function setXStep($xstep)
    {
        if (!NumberValidator::isValid($xstep)) {
            throw new InvalidArgumentException("XStep is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $xstep);
        $this->setEntry('XStep', $value);
        return $this;
    }

    /**
     * Set vertical spacing between patteern cells.
     *  
     * @param  mixed  $ystep
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Type\TilingPatternDictionaryType
     */
    public function setYStep($ystep)
    {
        if (!NumberValidator::isValid($ystep)) {
            throw new InvalidArgumentException("YStep is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $ystep);
        $this->setEntry('YStep', $value);
        return $this;
    }

    /**
     * Set resources.
     *  
     * @param  \Papier\Object\DictionaryObject  $resources
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\TilingPatternDictionaryType
     */
    public function setResources($resources)
    {
        if (!$resources instanceof DictionaryObject) {
            throw new InvalidArgumentException("Resources is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Resources', $resources);
        return $this;
    } 

    /**
     * Get resources.
     *  
     * @return \Papier\Type\DictionaryType
     */
    public function getResources()
    {
        if (!$this->hasEntry('Resources')) {
            $resources = Factory::create('Dictionary');
            $this->setResources($resources);
        }

        return $this->getEntry('Resources');
    }  
    
    /**
     * Set pattern matrix.
     *  
     * @param  array  $matrix
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     * @return \Papier\Type\TilingPatternDictionaryType
     */
    public function setMatrix($matrix)
    {
        if (!NumbersArrayValidator::isValid($matrix, 6)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }
    
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $this->setPatternType(PatternType::TILING_PATTERN);

        if (!$this->hasEntry('PaintType')) {
            throw new RuntimeException("PaintType is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('TilingType')) {
            throw new RuntimeException("TilingType is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('BBox')) {
            throw new RuntimeException("BBox is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('XStep')) {
            throw new RuntimeException("XStep is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('YStep')) {
            throw new RuntimeException("YStep is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Resources')) {
            throw new RuntimeException("Resources is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}