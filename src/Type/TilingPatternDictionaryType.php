<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;
use Papier\Validator\PaintTypeValidator;
use Papier\Validator\TilingTypeValidator;
use Papier\Validator\NumbersArrayValidator;

use Papier\Graphics\PatternType;
use Papier\Validator\NumberValidator;

use InvalidArgumentException;
use RuntimeException;

class TilingPatternDictionaryType extends PatternDictionaryType
{
    /**
     * Set paint type.
     *  
     * @param int $type
     * @return TilingPatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     */
    public function setPaintType(int $type): TilingPatternDictionaryType
    {
        if (!PaintTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("PaintType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $type);

        $this->setEntry('PaintType', $value);
        return $this;
    } 

    /**
     * Set tiling type.
     *  
     * @param int $type
     * @return TilingPatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     */
    public function setTilingType(int $type): TilingPatternDictionaryType
    {
        if (!TilingTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("TilingType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $type);

        $this->setEntry('TilingType', $value);
        return $this;
    }

    /**
     * Set boundaries of the pattern's cell bounding box.
     *  
     * @param RectangleType $bbox
     * @return TilingPatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleType'.
     */
    public function setBBox(RectangleType $bbox): TilingPatternDictionaryType
    {
        $this->setEntry('BBox', $bbox);
        return $this;
    }

    /**
     * Set horizontal spacing between patteern cells.
     *  
     * @param  mixed  $xstep
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return TilingPatternDictionaryType
     */
    public function setXStep($xstep): TilingPatternDictionaryType
    {
        if (!NumberValidator::isValid($xstep)) {
            throw new InvalidArgumentException("XStep is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $xstep);
        $this->setEntry('XStep', $value);
        return $this;
    }

    /**
     * Set vertical spacing between patteern cells.
     *  
     * @param  mixed  $ystep
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return TilingPatternDictionaryType
     */
    public function setYStep($ystep): TilingPatternDictionaryType
    {
        if (!NumberValidator::isValid($ystep)) {
            throw new InvalidArgumentException("YStep is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $ystep);
        $this->setEntry('YStep', $value);
        return $this;
    }

    /**
     * Set resources.
     *  
     * @param  DictionaryObject  $resources
     * @return TilingPatternDictionaryType
     */
    public function setResources(DictionaryObject $resources): TilingPatternDictionaryType
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
     * Set pattern matrix.
     *  
     * @param array $matrix
     * @return TilingPatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array'.
     */
    public function setMatrix(array $matrix): TilingPatternDictionaryType
    {
        if (!NumbersArrayValidator::isValid($matrix, 6)) {
            throw new InvalidArgumentException("Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumbersArrayType', $matrix);

        $this->setEntry('Matrix', $value);
        return $this;
    }
    
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
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