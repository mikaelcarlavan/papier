<?php

namespace Papier\Document;

use Papier\Base\IndirectObject;
use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;

use Papier\Validator\PageLayoutValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\PageModeValidator;
use Papier\Validator\BoolValidator;

use Papier\Document\PageLayout;
use Papier\Document\PageMode;

use Papier\Type\NumberTreeType;

use InvalidArgumentException;
use RuntimeException;

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
        $ver = new NameObject();
        $ver->setValue($version);
        $this->addEntry('Version', $ver);

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
            throw new InvalidArgumentException("Extensions is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Extensions', $extensions);
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
            throw new InvalidArgumentException("Pages is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $pages->setIndirect(true);
        $this->addEntry('Pages', $pages);
        return $this;
    }
  
    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param  \Papier\Type\NumberTreeType  $labels
     * @throws InvalidArgumentException if the provided argument is not of type 'NumberTreeType'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPageLabels($labels)
    {
        if (!$labels instanceof NumberTreeType) {
            throw new InvalidArgumentException("Labels is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('PageLabels', $labels);
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
            throw new InvalidArgumentException("Names is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Names', $names);
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
            throw new InvalidArgumentException("Dests is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $dests->setIndirect(true);
        $this->addEntry('Dests', $dests);
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
            throw new InvalidArgumentException("ViewPreferences is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('ViewerPreferences', $preferences);
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
            throw new InvalidArgumentException("Layout is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $lay = new NameObject();
        $lay->setValue($layout);
        $this->addEntry('PageLayout', $lay);

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
            throw new InvalidArgumentException("Mode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $mod = new NameObject();
        $mod->setValue($mode);
        $this->addEntry('PageMode', $mod);

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
            throw new InvalidArgumentException("Outlines is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $outlines->setIndirect(true);
        $this->addEntry('Outlines', $outlines);
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
            throw new InvalidArgumentException("Threads is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $threads->setIndirect(true);
        $this->addEntry('Threads', $threads);
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
            throw new InvalidArgumentException("Action is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('OpenAction', $action);
        return $this;
    } 

    /**
     * Set additional actions.
     *  
     * @param  \Papier\Object\DictionaryObject  $aa
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setAA($aa)
    {
        if (!$aa instanceof DictionaryObject) {
            throw new InvalidArgumentException("AA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('AA', $aa);
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
            throw new InvalidArgumentException("URI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('URI', $uri);
        return $this;
    } 

    /**
     * Set interactive form.
     *  
     * @param  \Papier\Object\DictionaryObject  $form
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setAcroForm($form)
    {
        if (!$form instanceof DictionaryObject) {
            throw new InvalidArgumentException("Form is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('AcroForm', $form);
        return $this;
    } 

    /**
     * Set metadata.
     *  
     * @param  \Papier\Object\StreamObject  $metadata
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setMetadata($metadata)
    {
        if (!$metadata instanceof StreamObject) {
            throw new InvalidArgumentException("Metadata is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $metadata->setIndirect(true);
        $this->addEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set document's structure tree root.
     *  
     * @param  \Papier\Object\DictionaryObject  $structTreeRoot
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setStructTreeRoot($structTreeRoot)
    {
        if (!$structTreeRoot instanceof DictionaryObject) {
            throw new InvalidArgumentException("StructTreeRoot is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('StructTreeRoot', $structTreeRoot);
        return $this;
    }


    /**
     * Set mark information.
     *  
     * @param  \Papier\Object\DictionaryObject  $markInfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setMarkInfo($markInfo)
    {
        if (!$markInfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("MarkInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('MarkInfo', $markInfo);
        return $this;
    }

    /**
     * Set document's language.
     *  
     * @param  string  $lang
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setLang($lang)
    {
        if (!StringValidator::isValid($lang)) {
            throw new InvalidArgumentException("Lang is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $lan = new StringObject();
        $lan->setValue($lang);

        $this->addEntry('Lang', $lan);
        return $this;
    }

    /**
     * Set spider info.
     *  
     * @param  \Papier\Object\DictionaryObject  $spiderInfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setSpiderInfo($spiderInfo)
    {
        if (!$spiderInfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("SpiderInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('SpiderInfo', $spiderInfo);
        return $this;
    }

    /**
     * Set output intents.
     *  
     * @param  \Papier\Object\ArrayObject  $outputIntents
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setOutputIntents($outputIntents)
    {
        if (!$outputIntents instanceof ArrayObject) {
            throw new InvalidArgumentException("OutputIntents is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('OutputIntents', $outputIntents);
        return $this;
    } 

    /**
     * Set piece info.
     *  
     * @param  \Papier\Object\DictionaryObject  $pieceInfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPieceInfo($pieceInfo)
    {
        if (!$pieceInfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("PieceInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('PieceInfo', $pieceInfo);
        return $this;
    }

    /**
     * Set OC properties.
     *  
     * @param  \Papier\Object\DictionaryObject  $ocProperties
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setOCProperties($ocProperties)
    {
        if (!$ocProperties instanceof DictionaryObject) {
            throw new InvalidArgumentException("OCProperties is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('OCProperties', $ocProperties);
        return $this;
    }

    /**
     * Set permissions.
     *  
     * @param  \Papier\Object\DictionaryObject  $perms
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setPerms($perms)
    {
        if (!$perms instanceof DictionaryObject) {
            throw new InvalidArgumentException("Perms is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Perms', $perms);
        return $this;
    }

    /**
     * Set legal.
     *  
     * @param  \Papier\Object\DictionaryObject  $legal
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setLegal($legal)
    {
        if (!$legal instanceof DictionaryObject) {
            throw new InvalidArgumentException("Legal is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Legal', $legal);
        return $this;
    }

    /**
     * Set requirements.
     *  
     * @param  \Papier\Object\ArrayObject  $requirements
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setRequirements($requirements)
    {
        if (!$requirements instanceof ArrayObject) {
            throw new InvalidArgumentException("Requirements is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Requirements', $requirements);
        return $this;
    } 

     /**
     * Set collection.
     *  
     * @param  \Papier\Object\DictionaryObject  $collection
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setCollection($collection)
    {
        if (!$collection instanceof DictionaryObject) {
            throw new InvalidArgumentException("Collection is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Collection', $collection);
        return $this;
    }

    /**
     * Set needs rendering.
     *  
     * @param  bool  $needsRendering
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setNeedsRendering($needsRendering)
    {
        if (!BoolValidator::isValid($needsRendering)) {
            throw new InvalidArgumentException("NeedsRendering is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $needs = new BooleanObject();
        $needs->setValue($needsRendering);

        $this->addEntry('NeedsRendering', $needs);
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

        if (!$dictionary->hasKey('Pages')) {
            throw new RuntimeException("Pages is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $type = new NameObject();
        $type->setValue('Catalog');
        $this->addEntry('Type', $type);

        // Set default values
        if (!$dictionary->hasKey('PageLayout')) {
            $this->setPageLayout(self::DEFAULT_PAGE_LAYOUT);
        }

        if (!$dictionary->hasKey('PageMode')) {
            $this->setPageMode(self::DEFAULT_PAGE_MODE);
        }

        if (!$dictionary->hasKey('NeedsRendering')) {
            $this->setNeedsRendering(false);
        }

        $value = $dictionary->write();
        
        return $value;
    }
}