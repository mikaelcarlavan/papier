<?php

namespace Papier\Type;

use Papier\Type\PatternDictionaryType;

use Papier\Factory\Factory;
use Papier\Validator\PaintTypeValidator;
use Papier\Validator\TilingTypeValidator;

use InvalidArgumentException;

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
}