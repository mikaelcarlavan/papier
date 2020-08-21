<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;
use Papier\Validator\PatternTypeValidator;

use InvalidArgumentException;

class PatternDictionaryType extends DictionaryObject
{
    /**
     * Set pattern type.
     *  
     * @param  int $type
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or a valid type.
     * @return \Papier\Type\PatternDictionaryType
     */
    public function setPatternType($type)
    {
        if (!PatternTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("PatternType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $type);

        $this->setEntry('PatternType', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $type = Factory::create('Name', 'Pattern');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}