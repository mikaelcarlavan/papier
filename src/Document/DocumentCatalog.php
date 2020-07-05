<?php

namespace Papier\Document;

use Papier\Base\IndirectObject;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use InvalidArgumentException;
use Exception;

class DocumentCatalog extends IndirectObject
{
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
     * Get catalog's dictionary.
     *
     * @return string
     */
    private function getDictionary()
    {
        return $this->getValue();
    }

    /**
     * Add entry to catalog's dictionnary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Document\DocumentCatalog
     */
    private function addEntry($key, $object)
    {
        $this->getDictionary()->setObjectForKey($key, $object);
        return $this;
    } 

    /**
     * Format catalog's content.
     *
     * @return string
     */
    public function format()
    {
        $type = new NameObject();
        $type->setValue('Catalog');
        $this->addEntry('Type', $type);

        $dictionary = $this->getDictionary();

        $value = $dictionary->write();
        
        return $value;
    }

    /**
     * Set PDF version.
     *  
     * @param  string  $version
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setVersion($version)
    {
        try {
            $ver = new NameObject();
            $ver->setValue($version);
            $this->addEntry('Version', $ver);
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    } 

    /**
     * Set extensions.
     *  
     * @param  \Papier\Object\DictionaryObject  $extensions
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setExtensions($extensions)
    {
        if (!$extensions instanceof DictionaryObject) {
            throw new InvalidArgumentException("Extensions is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Extensions', $extensions->getReference());
        return $this;
    } 


    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param  \Papier\Object\DictionaryObject  $pages
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPages($pages)
    {
        if (!$pages instanceof DictionaryObject) {
            throw new InvalidArgumentException("Pages is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Pages', $pages->getReference());
        return $this;
    } 
}