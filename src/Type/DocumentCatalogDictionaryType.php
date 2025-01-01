<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\NameType;
use Papier\Validator\BooleanValidator;
use Papier\Validator\PageLayoutValidator;
use Papier\Validator\PageModeValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class DocumentCatalogDictionaryType extends DictionaryType
{
    /**
     * Set PDF version.
     *  
     * @param NameType $version
     * @return DocumentCatalogDictionaryType
     */
    public function setVersion(NameType $version): DocumentCatalogDictionaryType
    {
        $this->setEntry('Version', $version);
        return $this;
    } 

    /**
     * Set extensions.
     *  
     * @param  ExtensionsDictionaryType  $extensions
     * @return DocumentCatalogDictionaryType
     */
    public function setExtensions(ExtensionsDictionaryType $extensions): DocumentCatalogDictionaryType
    {
        $this->setEntry('Extensions', $extensions);
        return $this;
    } 

    /**
     * Get extensions.
     *  
     * @return ExtensionsDictionaryType
     */
    public function getExtensions(): ExtensionsDictionaryType
    {
        if (!$this->hasEntry('Extensions')) {
			$extensions = Factory::create('Papier\Type\ExtensionsDictionaryType', null, true);
            $this->setExtensions($extensions);
        }

		/** @var ExtensionsDictionaryType $extensions */
		$extensions = $this->getEntry('Extensions');
        return $extensions;
    }

    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param DictionaryType $pages
     * @return DocumentCatalogDictionaryType
     */
    public function setPages(DictionaryType $pages): DocumentCatalogDictionaryType
    {
        $this->setEntry('Pages', $pages);
        return $this;
    }
  
    /**
     * Set page tree node (root of document's page tree).
     *  
     * @param  PageLabelsNumberTreeDictionaryType  $labels
     * @return DocumentCatalogDictionaryType
     */
    public function setPageLabels(PageLabelsNumberTreeDictionaryType $labels): DocumentCatalogDictionaryType
    {
        $this->setEntry('PageLabels', $labels);
        return $this;
    }

    /**
     * Set document's name dictionary.
     *  
     * @param DictionaryType $names
     * @return DocumentCatalogDictionaryType
     */
    public function setNames(DictionaryType $names): DocumentCatalogDictionaryType
    {
        $this->setEntry('Names', $names);
        return $this;
    } 

    /**
     * Set names and corresponding destinations.
     *  
     * @param DictionaryType $dests
     * @return DocumentCatalogDictionaryType
     */
    public function setDests(DictionaryType $dests): DocumentCatalogDictionaryType
    {
        $this->setEntry('Dests', $dests);
        return $this;
    } 

    /**
     * Set viewer preferences.
     *  
     * @param DictionaryType $preferences
     * @return DocumentCatalogDictionaryType
     */
    public function setViewerPreferences(DictionaryType $preferences): DocumentCatalogDictionaryType
    {
        $this->setEntry('ViewerPreferences', $preferences);
        return $this;
    }

	/**
	 * Get page labels
	 *
	 * @return PageLabelsNumberTreeDictionaryType
	 */
	public function getPageLabels(): PageLabelsNumberTreeDictionaryType
	{
		if (!$this->hasEntry('PageLabels')) {
			$pageLabels = Factory::create('Papier\Type\PageLabelsNumberTreeDictionaryType');
			$this->setPageLabels($pageLabels);
		}

		/** @var PageLabelsNumberTreeDictionaryType $pageLabels */
		$pageLabels = $this->getEntry('PageLabels');
		return $pageLabels;
	}

    /**
     * Get viewer preferences.
     *  
     * @return ViewerPreferencesDictionaryType
     */
    public function getViewerPreferences(): ViewerPreferencesDictionaryType
    {
        if (!$this->hasEntry('ViewerPreferences')) {
            $viewerPreferences = Factory::create('Papier\Type\ViewerPreferencesDictionaryType', null, true);
            $this->setViewerPreferences($viewerPreferences);
        }

		/** @var ViewerPreferencesDictionaryType $viewerPreferences */
		$viewerPreferences = $this->getEntry('ViewerPreferences');
        return $viewerPreferences;
    } 

    /**
     * Set page layout.
     *  
     * @param  string  $layout
     * @return DocumentCatalogDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid layout.
     */
    public function setPageLayout(string $layout): DocumentCatalogDictionaryType
    {
        if (!PageLayoutValidator::isValid($layout)) {
            throw new InvalidArgumentException("Layout is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\NameType', $layout);
        $this->setEntry('PageLayout', $value);

        return $this;
    } 

    /**
     * Set page mode.
     *  
     * @param  string  $mode
     * @return DocumentCatalogDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid mode.
     */
    public function setPageMode(string $mode): DocumentCatalogDictionaryType
    {
        if (!PageModeValidator::isValid($mode)) {
            throw new InvalidArgumentException("Mode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\NameType', $mode);
        $this->setEntry('PageMode', $value);

        return $this;
    } 

    /**
     * Set outlines.
     *  
     * @param DictionaryType $outlines
     * @return DocumentCatalogDictionaryType
     */
    public function setOutlines(DictionaryType $outlines): DocumentCatalogDictionaryType
    {
        $this->setEntry('Outlines', $outlines);
        return $this;
    } 

    /**
     * Set threads.
     *  
     * @param ArrayObject $threads
     * @return DocumentCatalogDictionaryType
     */
    public function setThreads(ArrayType $threads): DocumentCatalogDictionaryType
    {
        $this->setEntry('Threads', $threads);
        return $this;
    }

	/**
	 * Get threads
	 *
	 * @return ArrayType
	 */
	public function getThreads(): ArrayType
	{
		if (!$this->hasEntry('Threads')) {
			$threads = Factory::create('Papier\Type\Base\ArrayType');
			$this->setThreads($threads);
		}

		/** @var ArrayType $threads */
		$threads = $this->getEntry('Threads');
		return $threads;
	}

    /**
     * Set open action.
     *  
     * @param  mixed  $action
     * @return DocumentCatalogDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject' or 'DictionaryType'.
     */
    public function setOpenAction($action): DocumentCatalogDictionaryType
    {
        if (!$action instanceof ArrayObject && !$action instanceof DictionaryType) {
            throw new InvalidArgumentException("Action is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('OpenAction', $action);
        return $this;
    } 

    /**
     * Set additional actions.
     *  
     * @param DictionaryType $aa
     * @return DocumentCatalogDictionaryType
     */
    public function setAA(DictionaryType $aa): DocumentCatalogDictionaryType
    {
        $this->setEntry('AA', $aa);
        return $this;
    } 

    /**
     * Set URI.
     *  
     * @param  DictionaryType $uri
     * @return DocumentCatalogDictionaryType
     */
    public function setURI(DictionaryType $uri): DocumentCatalogDictionaryType
    {
        $this->setEntry('URI', $uri);
        return $this;
    } 

    /**
     * Set interactive form.
     *  
     * @param  DictionaryType $form
     * @return DocumentCatalogDictionaryType
     */
    public function setAcroForm(DictionaryType $form): DocumentCatalogDictionaryType
    {
        $this->setEntry('AcroForm', $form);
        return $this;
    } 

    /**
     * Set metadata.
     *  
     * @param StreamObject $metadata
     * @return DocumentCatalogDictionaryType
     */
    public function setMetadata(StreamObject $metadata): DocumentCatalogDictionaryType
    {
        $this->setEntry('Metadata', $metadata);
        return $this;
    } 

    /**
     * Set document's structure tree root.
     *  
     * @param  DictionaryType $structTreeRoot
     * @return DocumentCatalogDictionaryType
     */
    public function setStructTreeRoot(DictionaryType $structTreeRoot): DocumentCatalogDictionaryType
    {
        $this->setEntry('StructTreeRoot', $structTreeRoot);
        return $this;
    }


    /**
     * Set mark information.
     *  
     * @param  DictionaryType $markInfo
     * @return DocumentCatalogDictionaryType
     */
    public function setMarkInfo(DictionaryType $markInfo): DocumentCatalogDictionaryType
    {
        $this->setEntry('MarkInfo', $markInfo);
        return $this;
    }

    /**
     * Set document's language.
     *  
     * @param  string  $lang
     * @return DocumentCatalogDictionaryType
     *@throws InvalidArgumentException if the provided argument is not of type 'StringObject'.
     */
    public function setLang(string $lang): DocumentCatalogDictionaryType
	{
        if (!StringValidator::isValid($lang)) {
            throw new InvalidArgumentException("Lang is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\StringType', $lang);

        $this->setEntry('Lang', $value);
        return $this;
    }

    /**
     * Set spider info.
     *  
     * @param  DictionaryType $spiderInfo
     * @return DocumentCatalogDictionaryType
	 */
    public function setSpiderInfo(DictionaryType $spiderInfo): DocumentCatalogDictionaryType
	{
        $this->setEntry('SpiderInfo', $spiderInfo);
        return $this;
    }

    /**
     * Set output intents.
     *  
     * @param ArrayObject $outputIntents
     * @return DocumentCatalogDictionaryType
	 */
    public function setOutputIntents(ArrayObject $outputIntents): DocumentCatalogDictionaryType
	{
        $this->setEntry('OutputIntents', $outputIntents);
        return $this;
    } 

    /**
     * Set piece info.
     *  
     * @param  DictionaryType $pieceInfo
     * @return DocumentCatalogDictionaryType
	 */
    public function setPieceInfo(DictionaryType $pieceInfo): DocumentCatalogDictionaryType
	{
        $this->setEntry('PieceInfo', $pieceInfo);
        return $this;
    }

    /**
     * Set OC properties.
     *  
     * @param  DictionaryType $ocProperties
     * @return DocumentCatalogDictionaryType
	 */
    public function setOCProperties(DictionaryType $ocProperties): DocumentCatalogDictionaryType
	{
        $this->setEntry('OCProperties', $ocProperties);
        return $this;
    }

    /**
     * Set permissions.
     *  
     * @param  DictionaryType $perms
     * @return DocumentCatalogDictionaryType
	 */
    public function setPerms(DictionaryType $perms): DocumentCatalogDictionaryType
	{
        $this->setEntry('Perms', $perms);
        return $this;
    }

    /**
     * Set legal.
     *  
     * @param  DictionaryType $legal
     * @return DocumentCatalogDictionaryType
	 */
    public function setLegal(DictionaryType $legal): DocumentCatalogDictionaryType
	{
        $this->setEntry('Legal', $legal);
        return $this;
    }

    /**
     * Set requirements.
     *  
     * @param  ArrayObject $requirements
     * @return DocumentCatalogDictionaryType
	 */
    public function setRequirements(ArrayObject $requirements): DocumentCatalogDictionaryType
	{
        $this->setEntry('Requirements', $requirements);
        return $this;
    } 

     /**
     * Set collection.
     *  
     * @param  DictionaryType $collection
     * @return DocumentCatalogDictionaryType
	  */
    public function setCollection(DictionaryType $collection): DocumentCatalogDictionaryType
	{
        $this->setEntry('Collection', $collection);
        return $this;
    }

    /**
     * Set needs rendering.
     *  
     * @param  bool  $needs
     * @return DocumentCatalogDictionaryType
     *@throws InvalidArgumentException if the provided argument is not of type 'bool'.
	 */
    public function setNeedsRendering(bool $needs): DocumentCatalogDictionaryType
	{
        if (!BooleanValidator::isValid($needs)) {
            throw new InvalidArgumentException("NeedsRendering is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\BooleanType', $needs);
        $this->setEntry('NeedsRendering', $value);
        return $this;
    }

    /**
     * Format catalog's content.
     *
     * @return string
     */
    public function format(): string
    {
        if (!$this->hasEntry('Pages')) {
            throw new RuntimeException("Pages is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $type = Factory::create('Papier\Type\Base\NameType', 'Catalog');
        $this->setEntry('Type', $type);
        
        return parent::format();
    }
}