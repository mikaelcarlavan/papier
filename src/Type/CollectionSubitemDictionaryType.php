<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Validator\DateValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class CollectionSubitemDictionaryType extends DictionaryObject
{
    /**
     * Set data.
     *  
     * @param  mixed  $d
     * @return CollectionSubitemDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int', 'float', 'date' or 'string'.
     */
    public function setD($d): CollectionSubitemDictionaryType
    {
        if (!NumberValidator::isValid($d) && !StringValidator::isValid($d) && !DateValidator::isValid($d)) {
            throw new InvalidArgumentException("D is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (NumberValidator::isValid($d)) {
            $value = Factory::create('Integer', $d);
        } else if (StringValidator::isValid($d)) {
            $value = Factory::create('TextString', $d);
        } else {
            $value = Factory::create('Date', $d);
        }

        $this->setEntry('D', $value);
        return $this;
    } 

    /**
     * Set prefix.
     *  
     * @param string $p
     * @return CollectionSubitemDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setP(string $p): CollectionSubitemDictionaryType
    {
        $value = Factory::create('TextString', $p);

        $this->setEntry('P', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Name', 'CollectionSubitem');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}