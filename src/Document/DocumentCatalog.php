<?php

namespace Papier\Document;

use Papier\Base\IndirectObject;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use Papier\Validator\StringValidator;

use Papier\Document\PageLayout;

use InvalidArgumentException;
use Exception;

class DocumentCatalog extends IndirectObject
{
    /**
     * Default page layout.
     *
     * @var string
     */
    const DEFAULT_PAGE_LAYOUT = PageLayout::SINGLE_PAGE_LAYOUT;

    /**
     * Page layouts.
     *
     * @var array
     */
    const PAGE_LAYOUTS = array(
        PageLayout::SINGLE_PAGE_LAYOUT,
        PageLayout::ONE_COLUMN_LAYOUT,
        PageLayout::TWO_COLUMN_LEFT_LAYOUT,
        PageLayout::TWO_COLUMN_RIGHT_LAYOUT,
        PageLayout::TWO_PAGE_LEFT_LAYOUT,
        PageLayout::TWO_PAGE_RIGHT_LAYOUT,
    );

    /**
     * Create a new DocumentCatalog instance.
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
    
    /**
     * Set document's name dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $names
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setNames($names)
    {
        if (!$names instanceof DictionaryObject) {
            throw new InvalidArgumentException("Names is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Names', $names->getReference());
        return $this;
    } 

    /**
     * Set names and coresponding destimations.
     *  
     * @param  \Papier\Object\DictionaryObject  $dests
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setDests($dests)
    {
        if (!$dests instanceof DictionaryObject) {
            throw new InvalidArgumentException("Dests is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Dests', $dests->getReference());
        return $this;
    } 

    /**
     * Set viewer preferences.
     *  
     * @param  \Papier\Object\DictionaryObject  $preferences
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setViewerPreferences($preferences)
    {
        if (!$preferences instanceof DictionaryObject) {
            throw new InvalidArgumentException("ViewPreferences is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('ViewerPreferences', $preferences->getReference());
        return $this;
    } 

    /**
     * Set page layout.
     *  
     * @param  string  $layout
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPageLayout($layout)
    {
        if (!StringValidator::isValid($layout) || !in_array($layout, self::PAGE_LAYOUTS)) {
            throw new InvalidArgumentException("Layout is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        try {
            $lay = new NameObject();
            $lay->setValue($layout);
            $this->addEntry('PageLayout', $lay);
        } catch (Exception $e) {
            throw $e;
        }

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

        if (!$dictionary->hasKey('PageLayout')) {
            $this->setPageLayout(self::DEFAULT_PAGE_LAYOUT);
        }

        $value = $dictionary->write();
        
        return $value;
    }
}