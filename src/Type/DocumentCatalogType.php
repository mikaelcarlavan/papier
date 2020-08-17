<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;
use Papier\Object\BooleanObject;

use Papier\Validator\PageLayoutValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\PageModeValidator;
use Papier\Validator\BoolValidator;

use Papier\Document\PageLayout;
use Papier\Document\PageMode;

use Papier\Factory\Factory;

use Papier\Type\NumberTreeType;
use Papier\Type\DictionaryType;
use Papier\Type\ExtensionsDictionaryType;

use InvalidArgumentException;
use RuntimeException;

class DocumentCatalogType extends DictionaryType
{
    /**
     * Set PDF version.
     *  
     * @param  \Papier\Object\NameObject  $version
     * @throws InvalidArgumentException if the provided argument is not of type 'NameObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setVersion($version)
    {
        if (!$version instanceof NameObject) {
            throw new InvalidArgumentException("Version is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Version', $version);
        return $this;
    } 

    /**
     * Set extensions.
     *  
     * @param  \Papier\Type\ExtensionsDictionaryType  $extensions
     * @throws InvalidArgumentException if the provided argument is not of type 'ExtensionsDictionaryType'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setExtensions($extensions)
    {
        if (!$extensions instanceof ExtensionsDictionaryType) {
            throw new InvalidArgumentException("Extensions is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Extensions', $extensions);
        return $this;
    } 

    /**
     * Get extensions.
     *  
     * @return \Papier\Type\ExtensionsDictionaryType
     */
    public function getExtensions()
    {
        if (!$this->hasEntry('Extensions')) {
            $value = Factory::create('ExtensionsDictionary');
            $this->setExtensions('Extensions', $value);
        }

        return $this->getEntry('Extensions');
    } 
    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param  \Papier\Object\DictionaryObject  $pages
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setPages($pages)
    {
        if (!$pages instanceof DictionaryObject) {
            throw new InvalidArgumentException("Pages is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Pages', $pages);
        return $this;
    }
  
    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param  \Papier\Type\NumberTreeType  $labels
     * @throws InvalidArgumentException if the provided argument is not of type 'NumberTreeType'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setPageLabels($labels)
    {
        if (!$labels instanceof NumberTreeType) {
            throw new InvalidArgumentException("Labels is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('PageLabels', $labels);
        return $this;
    }
    /**
     * Set document's name dictionary.
     *  
     * @param  \Papier\Object\DictionaryObject  $names
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setNames($names)
    {
        if (!$names instanceof DictionaryObject) {
            throw new InvalidArgumentException("Names is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Names', $names);
        return $this;
    } 

    /**
     * Set names and coresponding destimations.
     *  
     * @param  \Papier\Object\DictionaryObject  $dests
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setDests($dests)
    {
        if (!$dests instanceof DictionaryObject) {
            throw new InvalidArgumentException("Dests is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Dests', $dests);
        return $this;
    } 

    /**
     * Set viewer preferences.
     *  
     * @param  \Papier\Object\DictionaryObject  $preferences
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setViewerPreferences($preferences)
    {
        if (!$preferences instanceof DictionaryObject) {
            throw new InvalidArgumentException("ViewPreferences is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ViewerPreferences', $preferences);
        return $this;
    } 

    /**
     * Set page layout.
     *  
     * @param  string  $layout
     * @throws InvalidArgumentException if the provided argument is not a valid layout.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setPageLayout($layout)
    {
        if (!PageLayoutValidator::isValid($layout)) {
            throw new InvalidArgumentException("Layout is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $layout, false);
        $this->setEntry('PageLayout', $value);

        return $this;
    } 

    /**
     * Set page mode.
     *  
     * @param  string  $mode
     * @throws InvalidArgumentException if the provided argument is not a valid mode.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setPageMode($mode)
    {
        if (!PageModeValidator::isValid($mode)) {
            throw new InvalidArgumentException("Mode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $mode, false);
        $this->setEntry('PageMode', $value);

        return $this;
    } 

    /**
     * Set outlines.
     *  
     * @param  \Papier\Object\DictionaryObject  $outlines
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setOutlines($outlines)
    {
        if (!$outlines instanceof DictionaryObject) {
            throw new InvalidArgumentException("Outlines is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Outlines', $outlines);
        return $this;
    } 

    /**
     * Set threads.
     *  
     * @param  \Papier\Object\ArrayObject  $threads
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setThreads($threads)
    {
        if (!$threads instanceof ArrayObject) {
            throw new InvalidArgumentException("Threads is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Threads', $threads);
        return $this;
    } 

    /**
     * Set open action.
     *  
     * @param  mixed  $action
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject' or 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
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
     * @param  \Papier\Object\DictionaryObject  $aa
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setAA($aa)
    {
        if (!$aa instanceof DictionaryObject) {
            throw new InvalidArgumentException("AA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('AA', $aa);
        return $this;
    } 

    /**
     * Set URI.
     *  
     * @param  \Papier\Object\DictionaryObject  $uri
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setURI($uri)
    {
        if (!$uri instanceof DictionaryObject) {
            throw new InvalidArgumentException("URI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('URI', $uri);
        return $this;
    } 

    /**
     * Set interactive form.
     *  
     * @param  \Papier\Object\DictionaryObject  $form
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setAcroForm($form)
    {
        if (!$form instanceof DictionaryObject) {
            throw new InvalidArgumentException("Form is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('AcroForm', $form);
        return $this;
    } 

    /**
     * Set metadata.
     *  
     * @param  \Papier\Object\StreamObject  $metadata
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setMetadata($metadata)
    {
        if (!$metadata instanceof StreamObject) {
            throw new InvalidArgumentException("Metadata is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set document's structure tree root.
     *  
     * @param  \Papier\Object\DictionaryObject  $structTreeRoot
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setStructTreeRoot($structTreeRoot)
    {
        if (!$structTreeRoot instanceof DictionaryObject) {
            throw new InvalidArgumentException("StructTreeRoot is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('StructTreeRoot', $structTreeRoot);
        return $this;
    }


    /**
     * Set mark information.
     *  
     * @param  \Papier\Object\DictionaryObject  $markInfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setMarkInfo($markInfo)
    {
        if (!$markInfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("MarkInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('MarkInfo', $markInfo);
        return $this;
    }

    /**
     * Set document's language.
     *  
     * @param  \Papier\Object\StringObject  $lang
     * @throws InvalidArgumentException if the provided argument is not of type 'StringObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setLang($lang)
    {
        if (!$lang instanceof StringObject) {
            throw new InvalidArgumentException("Lang is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Lang', $lang);
        return $this;
    }

    /**
     * Set spider info.
     *  
     * @param  \Papier\Object\DictionaryObject  $spiderInfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setSpiderInfo($spiderInfo)
    {
        if (!$spiderInfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("SpiderInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('SpiderInfo', $spiderInfo);
        return $this;
    }

    /**
     * Set output intents.
     *  
     * @param  \Papier\Object\ArrayObject  $outputIntents
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setOutputIntents($outputIntents)
    {
        if (!$outputIntents instanceof ArrayObject) {
            throw new InvalidArgumentException("OutputIntents is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('OutputIntents', $outputIntents);
        return $this;
    } 

    /**
     * Set piece info.
     *  
     * @param  \Papier\Object\DictionaryObject  $pieceInfo
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setPieceInfo($pieceInfo)
    {
        if (!$pieceInfo instanceof DictionaryObject) {
            throw new InvalidArgumentException("PieceInfo is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('PieceInfo', $pieceInfo);
        return $this;
    }

    /**
     * Set OC properties.
     *  
     * @param  \Papier\Object\DictionaryObject  $ocProperties
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setOCProperties($ocProperties)
    {
        if (!$ocProperties instanceof DictionaryObject) {
            throw new InvalidArgumentException("OCProperties is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('OCProperties', $ocProperties);
        return $this;
    }

    /**
     * Set permissions.
     *  
     * @param  \Papier\Object\DictionaryObject  $perms
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setPerms($perms)
    {
        if (!$perms instanceof DictionaryObject) {
            throw new InvalidArgumentException("Perms is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Perms', $perms);
        return $this;
    }

    /**
     * Set legal.
     *  
     * @param  \Papier\Object\DictionaryObject  $legal
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setLegal($legal)
    {
        if (!$legal instanceof DictionaryObject) {
            throw new InvalidArgumentException("Legal is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Legal', $legal);
        return $this;
    }

    /**
     * Set requirements.
     *  
     * @param  \Papier\Object\ArrayObject  $requirements
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setRequirements($requirements)
    {
        if (!$requirements instanceof ArrayObject) {
            throw new InvalidArgumentException("Requirements is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Requirements', $requirements);
        return $this;
    } 

     /**
     * Set collection.
     *  
     * @param  \Papier\Object\DictionaryObject  $collection
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setCollection($collection)
    {
        if (!$collection instanceof DictionaryObject) {
            throw new InvalidArgumentException("Collection is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Collection', $collection);
        return $this;
    }

    /**
     * Set needs rendering.
     *  
     * @param  bool  $needs
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\DocumentCatalogType
     */
    public function setNeedsRendering($needs)
    {
        if (!BoolValidator::isValid($needs)) {
            throw new InvalidArgumentException("NeedsRendering is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $needs, false);
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

        $type = Factory::create('Name', 'Catalog', false);
        $this->setEntry('Type', $type);
        
        return parent::format();
    }
}