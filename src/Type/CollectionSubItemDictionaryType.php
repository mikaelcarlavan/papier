<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\DateValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;

class CollectionSubItemDictionaryType extends DictionaryType
{
    /**
     * Set data.
     *  
     * @param  mixed  $d
     * @return CollectionSubItemDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'int', 'float', 'date' or 'string'.
     */
    public function setD($d): CollectionSubItemDictionaryType
    {
        if (NumberValidator::isValid($d)) {
            $value = Factory::create('Papier\Type\Base\IntegerType', $d);
        } else if (StringValidator::isValid($d)) {
            $value = Factory::create('Papier\Type\TextStringType', $d);
        } else if (DateValidator::isValid($d)) {
            $value = Factory::create('Papier\Type\Base\DateType', $d);
        } else {
			throw new InvalidArgumentException("D is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

        $this->setEntry('D', $value);
        return $this;
    } 

    /**
     * Set prefix.
     *  
     * @param string $p
     * @return CollectionSubItemDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setP(string $p): CollectionSubItemDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $p);

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
        $type = Factory::create('Papier\Type\Base\NameType', 'CollectionSubitem');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}