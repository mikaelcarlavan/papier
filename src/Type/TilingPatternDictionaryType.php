<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Graphics\PatternType;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\PaintTypeValidator;
use Papier\Validator\TilingTypeValidator;
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

        $value = Factory::create('Papier\Type\Base\IntegerType', $type);

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

        $value = Factory::create('Papier\Type\Base\IntegerType', $type);

        $this->setEntry('TilingType', $value);
        return $this;
    }

    /**
     * Set boundaries of the pattern's cell bounding box.
     *  
     * @param RectangleNumbersArrayType $bbox
     * @return TilingPatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'RectangleNumbersArrayType'.
     */
    public function setBBox(RectangleNumbersArrayType $bbox): TilingPatternDictionaryType
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
     * @param  DictionaryType  $resources
     * @return TilingPatternDictionaryType
     */
    public function setResources(DictionaryType $resources): TilingPatternDictionaryType
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
     * Set pattern matrix.
     *  
     * @param array<float> $matrix
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