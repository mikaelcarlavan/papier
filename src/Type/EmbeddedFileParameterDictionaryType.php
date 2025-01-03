<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\DateValidator;

class EmbeddedFileParameterDictionaryType extends DictionaryType
{

    /**
     * Set size.
     *  
     * @param int $size
     * @return EmbeddedFileParameterDictionaryType
     */
    public function setSize(int $size): EmbeddedFileParameterDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $size);

        $this->setEntry('Size', $value);
        return $this;
    } 

    /**
     * Set creation date.
     * 
     * @param \DateTime|string $date
     * @return EmbeddedFileParameterDictionaryType
     *@throws InvalidArgumentException if the provided argument is not a valid date.
     */
    public function setCreationDate(\DateTime|string $date): EmbeddedFileParameterDictionaryType
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("CreationDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\DateType', $date);

        $this->setEntry('CreationDate', $value);
        return $this;
    }

    /**
     * Set modification date.
     * 
     * @param \DateTime|string $date
     * @return EmbeddedFileParameterDictionaryType
     *@throws InvalidArgumentException if the provided argument is not a valid date.
     */
    public function setModDate(\DateTime|string $date): EmbeddedFileParameterDictionaryType
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("ModDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\TextStringType', $date);

        $this->setEntry('ModDate', $value);
        return $this;
    }

    /**
     * Set mac os files.
     *  
     * @param  DictionaryObject  $mac
     * @return EmbeddedFileParameterDictionaryType
     */
    public function setMac(DictionaryObject $mac): EmbeddedFileParameterDictionaryType
    {
        $this->setEntry('Mac', $mac);
        return $this;
    }

    /**
     * Set checksum.
     *  
     * @param string $checksum
     * @return EmbeddedFileParameterDictionaryType
     */
    public function setCheckSum(string $checksum): EmbeddedFileParameterDictionaryType
    {
        $value = Factory::create('Papier\Type\LiteralStringType', $checksum);

        $this->setEntry('CheckSum', $value);
        return $this;
    } 
}