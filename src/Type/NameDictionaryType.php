<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Type\Base\DictionaryType;

class NameDictionaryType extends DictionaryType
{
    /**
     * Set mapping to destinations.
     *  
     * @param NameTreeDictionaryType $dests
     * @return NameDictionaryType
     */
    public function setDests(NameTreeDictionaryType $dests): NameDictionaryType
    {
        $this->setEntry('Dests', $dests);
        return $this;
    }

    /**
     * Set mapping to annotations.
     *  
     * @param  NameTreeDictionaryType  $ap
     * @return NameDictionaryType
     */
    public function setAP(NameTreeDictionaryType $ap): NameDictionaryType
    {
        $this->setEntry('AP', $ap);
        return $this;
    }

    /**
     * Set mapping to document-level Javascript actions.
     *  
     * @param  NameTreeDictionaryType  $javascript
     * @return NameDictionaryType
     */
    public function setJavaScript(NameTreeDictionaryType $javascript): NameDictionaryType
    {
        $this->setEntry('JavaScript', $javascript);
        return $this;
    }


    /**
     * Set mapping to pages.
     *  
     * @param  NameTreeDictionaryType  $pages
     * @return NameDictionaryType
     */
    public function setPages(NameTreeDictionaryType $pages): NameDictionaryType
    {
        $this->setEntry('Pages', $pages);
        return $this;
    }


    /**
     * Set mapping to (invisible) pages.
     *  
     * @param  NameTreeDictionaryType  $templates
     * @return NameDictionaryType
     */
    public function setTemplates(NameTreeDictionaryType $templates): NameDictionaryType
    {
        $this->setEntry('Templates', $templates);
        return $this;
    }

    /**
     * Set IDS mapping to Web Capture content sets.
     *  
     * @param  NameTreeDictionaryType  $ids
     * @return NameDictionaryType
     */
    public function setIDS(NameTreeDictionaryType $ids): NameDictionaryType
    {
        $this->setEntry('IDS', $ids);
        return $this;
    }

    /**
     * Set URLS mapping to Web Capture content sets.
     *  
     * @param  NameTreeDictionaryType  $urls
     * @return NameDictionaryType
     */
    public function setURLS(NameTreeDictionaryType $urls): NameDictionaryType
    {
        $this->setEntry('URLS', $urls);
        return $this;
    }


    /**
     * Set mapping to file specifications for embedded file streams.
     *  
     * @param  NameTreeDictionaryType  $files
     * @return NameDictionaryType
     */
    public function setEmbeddedFiles(NameTreeDictionaryType $files): NameDictionaryType
    {
        $this->setEntry('EmbeddedFiles', $files);
        return $this;
    }

    /**
     * Set mapping to alternate presentations.
     *  
     * @param  NameTreeDictionaryType  $presentations
     * @return NameDictionaryType
     */
    public function setAlternatePresentations(NameTreeDictionaryType $presentations): NameDictionaryType
    {
        $this->setEntry('AlternatePresentations', $presentations);
        return $this;
    }

    /**
     * Set mapping to renditions objects.
     *  
     * @param  NameTreeDictionaryType  $renditions
     * @return NameDictionaryType
     */
    public function setRenditions(NameTreeDictionaryType $renditions): NameDictionaryType
    {
        $this->setEntry('Renditions', $renditions);
        return $this;
    }
}