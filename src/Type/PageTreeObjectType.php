<?php

namespace Papier\Type;

use Papier\Base\IndirectObject;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\IntegerObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;

use Papier\Validator\IntValidator;
use Papier\Validator\BoolValidator;

use InvalidArgumentException;

class PageTreeObjectType extends IndirectObject
{
    /**
     * Create a new DocumentCatalog instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->value = new DictionaryObject();
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
     * Add entry to page tree's object's dictionary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Document\PageTree\PageTreeObject
     */
    private function addEntry($key, $object)
    {
        $this->getDictionary()->setObjectForKey($key, $object);
        return $this;
    }

    /**
     * Set graphics state parameter mapping dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $state
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\ResourceDictionary
     */
    public function setExtGState($state)
    {
        if (!$state instanceof DictionaryObject) {
            throw new InvalidArgumentException("ExtGState is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state->setIndirect(true);
        $this->addEntry('ExtGState', $state);
        return $this;
    } 

    /**
     * Format catalog's content.
     *
     * @return string
     */
    public function format()
    {
        $dictionary = $this->getDictionary();


        $value = $dictionary->write();
        
        return $value;
    }
}