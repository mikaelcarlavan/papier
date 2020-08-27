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

class EmbeddedFileParameterDictionaryType extends DictionaryObject
{

    /**
     * Set size.
     *  
     * @param  int  $size
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\EmbeddedFileParameterDictionaryType
     */
    public function setSize($size)
    {
        if (!IntegerValidator::isValid($size)) {
            throw new InvalidArgumentException("Size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $size);

        $this->setEntry('Size', $value);
        return $this;
    } 

    /**
     * Set creation date.
     * 
     * @param   string  $date
     * @throws InvalidArgumentException if the provided argument is not a valid date.
     * @return \Papier\Type\EmbeddedFileParameterDictionaryType
     */
    public function setCreationDate($date)
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("CreationDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Date', $date);

        $this->setEntry('CreationDate', $value);
        return $this;
    }

    /**
     * Set modification date.
     * 
     * @param   string  $data
     * @throws InvalidArgumentException if the provided argument is not a valid date.
     * @return \Papier\Type\EmbeddedFileParameterDictionaryType
     */
    public function setModDate($date)
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("ModDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $date);

        $this->setEntry('ModDate', $value);
        return $this;
    }

    /**
     * Set mac os files.
     *  
     * @param  \Papier\Object\DictionaryObject  $mac
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\EmbeddedFileParameterDictionaryType
     */
    public function setMac($mac)
    {
        if (!$mac instanceof DictionaryObject) {
            throw new InvalidArgumentException("Mac is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Mac', $mac);
        return $this;
    }

    /**
     * Set checksum.
     *  
     * @param  string  $checksum
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\EmbeddedFileParameterDictionaryType
     */
    public function setCheckSum($checksum)
    {
        if (!StringValidator::isValid($checksum)) {
            throw new InvalidArgumentException("CheckSum is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('LiteralString', $checksum);

        $this->setEntry('CheckSum', $value);
        return $this;
    } 
}