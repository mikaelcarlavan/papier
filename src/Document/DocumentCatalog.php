<?php

namespace Papier\Document;

use Papier\Base\IndirectObject;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\ArrayObject;

use Papier\Validator\PageLayoutValidator;
use Papier\Document\PageLayout;
use Papier\Validator\PageModeValidator;
use Papier\Document\PageMode;

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
     * Default page mode.
     *
     * @var string
     */
    const DEFAULT_PAGE_MODE = PageMode::USE_NONE_MODE;

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
     * @throws InvalidArgumentException if the provided argument is not a valid layout.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPageLayout($layout)
    {
        if (!PageLayoutValidator::isValid($layout)) {
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
     * Set page mode.
     *  
     * @param  string  $mode
     * @throws InvalidArgumentException if the provided argument is not a valid mode.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPageMode($mode)
    {
        if (!PageModeValidator::isValid($mode)) {
            throw new InvalidArgumentException("Mode is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        try {
            $mod = new NameObject();
            $mod->setValue($mode);
            $this->addEntry('PageMode', $mod);
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    } 

    /**
     * Set outlines.
     *  
     * @param  \Papier\Object\DictionaryObject  $outlines
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setOutlines($outlines)
    {
        if (!$outlines instanceof DictionaryObject) {
            throw new InvalidArgumentException("Outlines is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Outlines', $outlines->getReference());
        return $this;
    } 

    /**
     * Set threads.
     *  
     * @param  \Papier\Object\ArrayObject  $threads
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setThreads($threads)
    {
        if (!$threads instanceof ArrayObject) {
            throw new InvalidArgumentException("Threads is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('Threads', $threads->getReference());
        return $this;
    } 

    /**
     * Set open action.
     *  
     * @param  mixed  $action
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject' or 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setOpenAction($action)
    {
        if (!$action instanceof ArrayObject && !$action instanceof DictionaryObject) {
            throw new InvalidArgumentException("Action is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('OpenAction', $action->getReference());
        return $this;
    } 

    /**
     * Set AA.
     *  
     * @param  \Papier\Object\DictionaryObject  $aa
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setAA($aa)
    {
        if (!$aa instanceof DictionaryObject) {
            throw new InvalidArgumentException("AA is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('AA', $aa->getReference());
        return $this;
    } 

    /**
     * Set URI.
     *  
     * @param  \Papier\Object\DictionaryObject  $uri
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setURI($uri)
    {
        if (!$uri instanceof DictionaryObject) {
            throw new InvalidArgumentException("URI is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('URI', $uri->getReference());
        return $this;
    } 

    /**
     * Set AcroForm.
     *  
     * @param  \Papier\Object\DictionaryObject  $form
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setAcroForm($form)
    {
        if (!$form instanceof DictionaryObject) {
            throw new InvalidArgumentException("AcroForm is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $this->addEntry('AcroForm', $form->getReference());
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

        // Set default values
        if (!$dictionary->hasKey('PageLayout')) {
            $this->setPageLayout(self::DEFAULT_PAGE_LAYOUT);
        }

        if (!$dictionary->hasKey('PageMode')) {
            $this->setPageMode(self::DEFAULT_PAGE_MODE);
        }

        $value = $dictionary->write();
        
        return $value;
    }
}