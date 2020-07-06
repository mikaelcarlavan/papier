<?php

namespace Papier\File;

use Papier\Base\Object;
use Papier\Validator\IntValidator;
use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use InvalidArgumentException;
use RuntimeException;

class FileTrailer extends Object
{
     /**
     * Offset in bytes (from the beginning of the file) to the cross reference table.
     *
     * @var int
     */
    private $crossReferenceOffset = 0;

    /**
     * Create a new FileTrailer instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = new DictionaryObject();
        parent::__construct();
    } 

    /**
     * Get trailer's dictionary.
     *
     * @return \Papier\Object\DictionaryObject
     */
    private function getDictionary()
    {
        return $this->getValue();
    }

    /**
     * Add entry to trailer's dictionnary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\File\FileTrailer
     */
    private function addEntry($key, $object)
    {
        $this->getDictionary()->setObjectForKey($key, $object);
        return $this;
    } 

    /**
     * Get trailer's cross reference offset.
     *
     * @return int
     */
    public function getCrossReferenceOffset()
    {
        return $this->crossReferenceOffset ?? 0;
    }

    /**
     * Set cross reference offset.
     *  
     * @param  int  $crossReferenceOffset
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\File\FileTrailer
     */
    public function setCrossReferenceOffset($crossReferenceOffset)
    {
        if (!IntValidator::isValid($crossReferenceOffset)) {
            throw new InvalidArgumentException("Cross reference offset is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->crossReferenceOffset = $crossReferenceOffset;
        return $this;
    } 

    /**
     * Set size (total number of entries in the file's cross-reference table).
     *  
     * @param  int  $size
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\File\FileTrailer
     */
    public function setSize($size)
    {
        if (!IntValidator::isValid($size)) {
            throw new InvalidArgumentException("Size is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Size', $size);
        return $this;
    } 

    /**
     * Set prev (byte offset in the decoded stream from the beginning of the file to the beginning of the previous cross-reference section).
     *  
     * @param  int  $prev
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\File\FileTrailer
     */
    public function setPrev($prev)
    {
        if (!IntValidator::isValid($prev)) {
            throw new InvalidArgumentException("Prev is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Prev', $prev);
        return $this;
    } 

    /**
     * Set root (catalog dictionary for the PDF document contained in the file).
     *  
     * @param  \Papier\Object\DictionaryObject  $root
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\File\FileTrailer
     */
    public function setRoot($root)
    {
        if (!$root instanceof DictionaryObject) {
            throw new InvalidArgumentException("Root is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Root', $root->getReference());
        return $this;
    } 


    /**
     * Set encrypt (document’s encryption dictionary).
     *  
     * @param  \Papier\Object\DictionaryObject  $encrypt
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\File\FileTrailer
     */
    public function setEncrypt($encrypt)
    {
        if (!$encrypt instanceof DictionaryObject) {
            throw new InvalidArgumentException("Encrypt is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Encrypt', $encrypt->getReference());
        return $this;
    } 


    /**
     * Set info (document’s information dictionary).
     *  
     * @param  \Papier\Object\DictionaryObject  $info
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\File\FileTrailer
     */
    public function setInfo($info)
    {
        if (!$info instanceof DictionaryObject) {
            throw new InvalidArgumentException("Info is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Info', $info->getReference());
        return $this;
    } 

    /**
     * Set ID (array of two byte-strings constituting a file identifier).
     *  
     * @param  \Papier\Object\ArrayObject  $ID
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\File\FileTrailer
     */
    public function setID($ID)
    {
        if (!$ID instanceof ArrayObject) {
            throw new InvalidArgumentException("ID is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('ID', $ID->write());
        return $this;
    } 

    /**
     * Format trailer's content.
     *
     * @return string
     */
    public function format()
    {
        $dictionary = $this->getDictionary();

        if (!$dictionary->hasKey('Root')) {
            throw new RuntimeException("Root is missing. See ".get_class($this)." class's documentation for possible values.");
        }

        if (!$dictionary->hasKey('Size')) {
            throw new RuntimeException("Size is missing. See ".get_class($this)." class's documentation for possible values.");
        }

        $value = 'trailer' . self::EOL_MARKER;
        $value .= $dictionary->write();
        $value .= 'startxref' . self::EOL_MARKER;
        $value .= $this->getCrossReferenceOffset() . self::EOL_MARKER;
        $value .= '%%EOF';
        
        return $value;
    }
}