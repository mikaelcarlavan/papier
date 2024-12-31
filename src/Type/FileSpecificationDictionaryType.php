<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\ByteStringsArrayValidator;
use RuntimeException;

class FileSpecificationDictionaryType extends DictionaryType
{
    /**
     * Set file system.
     *  
     * @param  string  $fs
     * @return FileSpecificationDictionaryType
     */
    public function setFS(string $fs): FileSpecificationDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\NameType', $fs);

        $this->setEntry('FS', $value);
        return $this;
    }

    /**
     * Set file specification.
     *
     * @param string $f
     * @return FileSpecificationDictionaryType
     */
    public function setF(string $f): FileSpecificationDictionaryType
    {
        $value = Factory::create('Papier\Type\FileSpecificationStringType', $f);

        $this->setEntry('F', $value);
        return $this;
    } 

    /**
     * Set unicode file specification.
     *  
     * @param string $uf
     * @return FileSpecificationDictionaryType
     */
    public function setUF(string $uf): FileSpecificationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', Factory::create('Papier\Type\FileSpecificationStringType', $uf)->getConvertedValue());

        $this->setEntry('UF', $value);
        return $this;
    } 

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param array<string> $id
     * @return FileSpecificationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'string'.
     */
    public function setID(array $id): FileSpecificationDictionaryType
    {
        if (!ByteStringsArrayValidator::isValid($id, 2)) {
            throw new InvalidArgumentException("ID is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ID', $id);
        return $this;
    }

    /**
     * Set volatile.
     *  
     * @param bool $v
     * @return FileSpecificationDictionaryType
     */
    public function setV(bool $v): FileSpecificationDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\BooleanType', $v);
        $this->setEntry('V', $value);
        return $this;
    }


    /**
     * Set embed files.
     *  
     * @param DictionaryObject $ef
     * @return FileSpecificationDictionaryType
     */
    public function setEF(DictionaryObject $ef): FileSpecificationDictionaryType
    {
        $this->setEntry('EF', $ef);
        return $this;
    }

    /**
     * Set relative files.
     *  
     * @param DictionaryObject $rf
     * @return FileSpecificationDictionaryType
     */
    public function setRF(DictionaryObject $rf): FileSpecificationDictionaryType
    {
        $this->setEntry('RF', $rf);
        return $this;
    }

    /**
     * Set description.
     *  
     * @param  string  $desc
     * @return FileSpecificationDictionaryType
     */
    public function setDesc(string $desc): FileSpecificationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $desc);

        $this->setEntry('Desc', $value);
        return $this;
    } 

    /**
     * Set collection items.
     *  
     * @param DictionaryObject $ci
     * @return FileSpecificationDictionaryType
     */
    public function setCI(DictionaryObject $ci): FileSpecificationDictionaryType
    {
        $this->setEntry('CI', $ci);
        return $this;
    }


    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        if (!$this->hasEntry('F') && !$this->hasEntry('UF')) {
            throw new RuntimeException("UF and F are missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('RF') && !$this->hasEntry('EF')) {
            throw new RuntimeException("EF is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('EF') || $this->hasEntry('RF')) {
            $type = Factory::create('Papier\Type\Base\NameType', 'Filespec');
            $this->setEntry('Type', $type);
        }

        return parent::format();
    }
}