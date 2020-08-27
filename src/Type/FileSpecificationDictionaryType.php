<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;
use Papier\Object\NameObject;
use Papier\Object\StreamObject;

use Papier\Validator\IntegerValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\ByteStringsArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class FileSpecificationDictionaryType extends DictionaryObject
{
    /**
     * Set file system.
     *  
     * @param  string  $fs
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setFS($fs)
    {
        if (!StringValidator::isValid($space)) {
            throw new InvalidArgumentException("FS is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $fs);

        $this->setEntry('FS', $value);
        return $this;
    } 

    /**
     * Set file specification.
     *  
     * @param  string  $fs
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setF($f)
    {
        if (!StringValidator::isValid($f)) {
            throw new InvalidArgumentException("F is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('FileSpecificationString', $f);

        $this->setEntry('FS', $value);
        return $this;
    } 

    /**
     * Set unicode file specification.
     *  
     * @param  string  $uf
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setUF($uf)
    {
        if (!StringValidator::isValid($uf)) {
            throw new InvalidArgumentException("UF is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', Factory::create('FileSpecificationString', $uf)->getConvertedValue());

        $this->setEntry('UF', $value);
        return $this;
    } 

    /**
     * Set digital identifier of the page's parent web capture content set.
     *  
     * @param  array  $id
     * @throws InvalidArgumentException if the provided argument is not of type 'array' or if each element of the array is not of type 'string'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setID($id)
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
     * @param  bool  $v
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setV($v)
    {
        if (!BooleanValidator::isValid($v)) {
            throw new InvalidArgumentException("V is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $v);
        $this->setEntry('V', $value);
        return $this;
    }


    /**
     * Set embed files.
     *  
     * @param  \Papier\Object\DictionaryObject  $ef
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setEF($ef)
    {
        if (!$ef instanceof DictionaryObject) {
            throw new InvalidArgumentException("EF is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('EF', $ef);
        return $this;
    }

    /**
     * Set relative files.
     *  
     * @param  \Papier\Object\DictionaryObject  $rf
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setRF($rf)
    {
        if (!$rf instanceof DictionaryObject) {
            throw new InvalidArgumentException("RF is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('RF', $rf);
        return $this;
    }

    /**
     * Set description.
     *  
     * @param  string  $desc
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setDesc($desc)
    {
        if (!StringValidator::isValid($desc)) {
            throw new InvalidArgumentException("Desc is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $desc);

        $this->setEntry('Desc', $value);
        return $this;
    } 

    /**
     * Set collection items.
     *  
     * @param  \Papier\Object\DictionaryObject  $ci
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\FileSpecificationDictionaryType
     */
    public function setCI($ci)
    {
        if (!$ci instanceof DictionaryObject) {
            throw new InvalidArgumentException("CI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('CI', $ci);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $type = Factory::create('Name', 'Filespec');
        $this->setEntry('Type', $type);

        if (!$this->hasEntry('F') && !$this->hasEntry('UF')) {
            throw new RuntimeException("UF and F are missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('RF') && !$this->hasEntry('EF')) {
            throw new RuntimeException("EF is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}