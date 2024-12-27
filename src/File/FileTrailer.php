<?php

namespace Papier\File;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Repository\Repository;
use Papier\Type\DocumentInformationDictionaryType;
use Papier\Validator\IntegerValidator;
use RuntimeException;

class FileTrailer extends DictionaryObject
{
     /**
     * End-of-file marker.
     *
     * @var string
     */
    const EOF_MARKER = "%%EOF";

     /**
     * Offset in bytes (from the beginning of the file) to the cross reference table.
     *
     * @var int
     */
    private int $crossReferenceOffset = 0;

    /**
     * Get trailer's cross-reference offset.
     *
     * @return int
     */
    public function getCrossReferenceOffset(): int
    {
        return $this->crossReferenceOffset;
    }

    /**
     * Set cross reference offset.
     *  
     * @param  int  $crossReferenceOffset
     * @return FileTrailer
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setCrossReferenceOffset(int $crossReferenceOffset): FileTrailer
    {
        if (!IntegerValidator::isValid($crossReferenceOffset, 0)) {
            throw new InvalidArgumentException("Cross reference offset is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->crossReferenceOffset = $crossReferenceOffset;
        return $this;
    } 

    /**
     * Set size (total number of entries in the file's cross-reference table).
     *  
     * @param  int  $size
     * @return FileTrailer
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setSize(int $size): FileTrailer
    {
        if (!IntegerValidator::isValid($size, 0)) {
            throw new InvalidArgumentException("Size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $size);

        $this->setEntry('Size', $value);
        return $this;
    } 

    /**
     * Set prev (byte offset in the decoded stream from the beginning of the file to the beginning of the previous cross-reference section).
     *  
     * @param  int  $prev
     * @return FileTrailer
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setPrev(int $prev): FileTrailer
    {
        if (!IntegerValidator::isValid($prev, 0)) {
            throw new InvalidArgumentException("Prev is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $prev);

        $this->setEntry('Prev', $value);
        return $this;
    }

    /**
     * Set root (catalog dictionary for the PDF document contained in the file).
     *
     * @param DictionaryObject $root
     * @return FileTrailer
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setRoot(DictionaryObject $root): FileTrailer
    {
        $this->setEntry('Root', $root);
        return $this;
    } 


    /**
     * Set encrypt (document’s encryption dictionary).
     *  
     * @param DictionaryObject $encrypt
     * @return FileTrailer
     *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setEncrypt(DictionaryObject $encrypt): FileTrailer
    {
        $this->setEntry('Encrypt', $encrypt);
        return $this;
    } 


    /**
     * Set info (document’s information dictionary).
     *  
     * @param DictionaryObject $info
     * @return FileTrailer
     *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     */
    public function setInfo(DictionaryObject $info): FileTrailer
    {
        $this->setEntry('Info', $info);
        return $this;
    } 

    /**
     * Get info.
     *  
     * @return DocumentInformationDictionaryType
     */
    public function getInfo(): DocumentInformationDictionaryType
    {
        if (!$this->hasEntry('Info')) {
            $value = Factory::create('Papier\Type\DocumentInformationDictionaryType', null, true);
            $this->setInfo($value);
        }

		/** @var DocumentInformationDictionaryType $info */
		$info = $this->getEntry('Info');
        return $info;
    } 

    /**
     * Set ID (array of two byte-strings constituting a file identifier).
     *  
     * @param ArrayObject $ID
     * @return FileTrailer
     */
    public function setID(ArrayObject $ID): FileTrailer
    {
        $this->setEntry('ID', $ID);
        return $this;
    } 

    /**
     * Format trailer's content.
     *
     * @return string
     */
    public function format(): string
    {
        $repository = Repository::getInstance();
        $this->setSize(count($repository->getObjects()));

        if (!$this->hasEntry('Root')) {
            throw new RuntimeException("Root is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Size')) {
            throw new RuntimeException("Size is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = 'trailer' . self::EOL_MARKER;
        $value .= parent::format() . self::EOL_MARKER;
        $value .= 'startxref' . self::EOL_MARKER;
        $value .= $this->getCrossReferenceOffset() . self::EOL_MARKER;
        $value .= self::EOF_MARKER;
        $value .= self::EOL_MARKER;

        return $value;
    }
}