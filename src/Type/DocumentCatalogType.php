<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;

use Papier\Validator\PageLayoutValidator;
use Papier\Validator\PageModeValidator;
use Papier\Validator\BooleanValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use Papier\Validator\StringValidator;
use RuntimeException;

class DocumentCatalogType extends DictionaryType
{
    /**
     * Set PDF version.
     *  
     * @param NameObject $version
     * @return DocumentCatalogType
     */
    public function setVersion(NameObject $version)
    {
        $this->setEntry('Version', $version);
        return $this;
    } 

    /**
     * Set extensions.
     *  
     * @param  ExtensionsDictionaryType  $extensions
     * @return DocumentCatalogType
     */
    public function setExtensions(ExtensionsDictionaryType $extensions)
    {
        $this->setEntry('Extensions', $extensions);
        return $this;
    } 

    /**
     * Get extensions.
     *  
     * @return ExtensionsDictionaryType
     */
    public function getExtensions()
    {
        if (!$this->hasEntry('Extensions')) {
            $value = Factory::create('ExtensionsDictionary', null, true);
            $this->setExtensions('Extensions', $value);
        }

        return $this->getEntry('Extensions');
    }

    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param DictionaryObject $pages
     * @return DocumentCatalogType
     */
    public function setPages(DictionaryObject $pages)
    {
        $this->setEntry('Pages', $pages);
        return $this;
    }
  
    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param  NumberTreeType  $labels
     * @return DocumentCatalogType
     */
    public function setPageLabels(NumberTreeType $labels)
    {
        $this->setEntry('PageLabels', $labels);
        return $this;
    }

    /**
     * Set document's name dictionary.
     *  
     * @param DictionaryObject $names
     * @return DocumentCatalogType
     */
    public function setNames(DictionaryObject $names)
    {
        $this->setEntry('Names', $names);
        return $this;
    } 

    /**
     * Set names and corresponding destinations.
     *  
     * @param DictionaryObject $dests
     * @return DocumentCatalogType
     */
    public function setDests(DictionaryObject $dests)
    {
        $this->setEntry('Dests', $dests);
        return $this;
    } 

    /**
     * Set viewer preferences.
     *  
     * @param DictionaryObject $preferences
     * @return DocumentCatalogType
     */
    public function setViewerPreferences(DictionaryObject $preferences)
    {
        $this->setEntry('ViewerPreferences', $preferences);
        return $this;
    } 

    /**
     * Get viewer preferences.
     *  
     * @return ViewerPreferencesDictionaryType
     */
    public function getViewerPreferences()
    {
        if (!$this->hasEntry('ViewerPreferences')) {
            $value = Factory::create('ViewerPreferencesDictionary', null, true);
            $this->setViewerPreferences($value);
        }

        return $this->getEntry('ViewerPreferences');
    } 

    /**
     * Set page layout.
     *  
     * @param  string  $layout
     * @return DocumentCatalogType
     * @throws InvalidArgumentException if the provided argument is not a valid layout.
     */
    public function setPageLayout(string $layout)
    {
        if (!PageLayoutValidator::isValid($layout)) {
            throw new InvalidArgumentException("Layout is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $layout);
        $this->setEntry('PageLayout', $value);

        return $this;
    } 

    /**
     * Set page mode.
     *  
     * @param  string  $mode
     * @return DocumentCatalogType
     * @throws InvalidArgumentException if the provided argument is not a valid mode.
     */
    public function setPageMode(string $mode)
    {
        if (!PageModeValidator::isValid($mode)) {
            throw new InvalidArgumentException("Mode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $mode);
        $this->setEntry('PageMode', $value);

        return $this;
    } 

    /**
     * Set outlines.
     *  
     * @param DictionaryObject $outlines
     * @return DocumentCatalogType
     */
    public function setOutlines(DictionaryObject $outlines)
    {
        $this->setEntry('Outlines', $outlines);
        return $this;
    } 

    /**
     * Set threads.
     *  
     * @param ArrayObject $threads
     * @return DocumentCatalogType
     */
    public function setThreads(ArrayObject $threads)
    {
        $this->setEntry('Threads', $threads);
        return $this;
    } 

    /**
     * Set open action.
     *  
     * @param  mixed  $action
     * @return DocumentCatalogType
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject' or 'DictionaryObject'.
     */
    public function setOpenAction($action)
    {
        if (!$action instanceof ArrayObject && !$action instanceof DictionaryObject) {
            throw new InvalidArgumentException("Action is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('OpenAction', $action);
        return $this;
    } 

    /**
     * Set additional actions.
     *  
     * @param DictionaryObject $aa
     * @return DocumentCatalogType
     */
    public function setAA(DictionaryObject $aa)
    {
        $this->setEntry('AA', $aa);
        return $this;
    } 

    /**
     * Set URI.
     *  
     * @param  DictionaryObject $uri
     * @return DocumentCatalogType
     */
    public function setURI(DictionaryObject $uri)
    {
        $this->setEntry('URI', $uri);
        return $this;
    } 

    /**
     * Set interactive form.
     *  
     * @param  DictionaryObject $form
     * @return DocumentCatalogType
     */
    public function setAcroForm(DictionaryObject $form)
    {
        $this->setEntry('AcroForm', $form);
        return $this;
    } 

    /**
     * Set metadata.
     *  
     * @param StreamObject $metadata
     * @return DocumentCatalogType
     */
    public function setMetadata(StreamObject $metadata)
    {
        $this->setEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set document's structure tree root.
     *  
     * @param  DictionaryObject $structTreeRoot
     * @return DocumentCatalogType
     */
    public function setStructTreeRoot(DictionaryObject $structTreeRoot)
    {
        $this->setEntry('StructTreeRoot', $structTreeRoot);
        return $this;
    }


    /**
     * Set mark information.
     *  
     * @param  DictionaryObject $markInfo
     * @return DocumentCatalogType
     */
    public function setMarkInfo(DictionaryObject $markInfo)
    {
        $this->setEntry('MarkInfo', $markInfo);
        return $this;
    }

    /**
     * Set document's language.
     *  
     * @param  string  $lang
     * @throws InvalidArgumentException if the provided argument is not of type 'StringObject'.
     * @return DocumentCatalogType
     */
    public function setLang(string $lang)
    {
        if (!StringValidator::isValid($lang)) {
            throw new InvalidArgumentException("Lang is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('String', $lang);

        $this->setEntry('Lang', $value);
        return $this;
    }

    /**
     * Set spider info.
     *  
     * @param  DictionaryObject $spiderInfo
     * @return DocumentCatalogType
     */
    public function setSpiderInfo(DictionaryObject $spiderInfo)
    {
        $this->setEntry('SpiderInfo', $spiderInfo);
        return $this;
    }

    /**
     * Set output intents.
     *  
     * @param ArrayObject $outputIntents
     * @return DocumentCatalogType
     */
    public function setOutputIntents(ArrayObject $outputIntents)
    {
        $this->setEntry('OutputIntents', $outputIntents);
        return $this;
    } 

    /**
     * Set piece info.
     *  
     * @param  DictionaryObject $pieceInfo
     * @return DocumentCatalogType
     */
    public function setPieceInfo(DictionaryObject $pieceInfo)
    {
        $this->setEntry('PieceInfo', $pieceInfo);
        return $this;
    }

    /**
     * Set OC properties.
     *  
     * @param  DictionaryObject $ocProperties
     * @return DocumentCatalogType
     */
    public function setOCProperties(DictionaryObject $ocProperties)
    {
        $this->setEntry('OCProperties', $ocProperties);
        return $this;
    }

    /**
     * Set permissions.
     *  
     * @param  DictionaryObject $perms
     * @return DocumentCatalogType
     */
    public function setPerms(DictionaryObject $perms)
    {
        $this->setEntry('Perms', $perms);
        return $this;
    }

    /**
     * Set legal.
     *  
     * @param  DictionaryObject $legal
     * @return DocumentCatalogType
     */
    public function setLegal(DictionaryObject $legal)
    {
        $this->setEntry('Legal', $legal);
        return $this;
    }

    /**
     * Set requirements.
     *  
     * @param  ArrayObject $requirements
     * @return DocumentCatalogType
     */
    public function setRequirements(ArrayObject $requirements)
    {
        $this->setEntry('Requirements', $requirements);
        return $this;
    } 

     /**
     * Set collection.
     *  
     * @param  DictionaryObject $collection
     * @return DocumentCatalogType
     */
    public function setCollection(DictionaryObject $collection)
    {
        $this->setEntry('Collection', $collection);
        return $this;
    }

    /**
     * Set needs rendering.
     *  
     * @param  bool  $needs
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return DocumentCatalogType
     */
    public function setNeedsRendering(bool $needs)
    {
        if (!BooleanValidator::isValid($needs)) {
            throw new InvalidArgumentException("NeedsRendering is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $needs);
        $this->setEntry('NeedsRendering', $value);
        return $this;
    }

    /**
     * Format catalog's content.
     *
     * @return string
     */
    public function format()
    {
        if (!$this->hasEntry('Pages')) {
            throw new RuntimeException("Pages is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $type = Factory::create('Name', 'Catalog');
        $this->setEntry('Type', $type);
        
        return parent::format();
    }
}