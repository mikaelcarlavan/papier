<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;
use Papier\Validator\PatternTypeValidator;

use InvalidArgumentException;
use RuntimeException;

class PatternDictionaryType extends DictionaryObject
{
    /**
     * Set pattern type.
     *  
     * @param int $type
     * @return PatternDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     */
    public function setPatternType(int $type): PatternDictionaryType
    {
        if (!PatternTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("PatternType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $type);

        $this->setEntry('PatternType', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        if (!$this->hasEntry('PatternType')) {
            throw new RuntimeException("PatternType is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $type = Factory::create('Papier\Type\NameType', 'Pattern');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}