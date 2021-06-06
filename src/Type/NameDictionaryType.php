<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

class NameDictionaryType extends DictionaryObject
{
    /**
     * Set mapping to destinations.
     *  
     * @param NameTreeType $dests
     * @return NameDictionaryType
     */
    public function setDests(NameTreeType $dests): NameDictionaryType
    {
        $this->setEntry('Dests', $dests);
        return $this;
    }

    /**
     * Set mapping to annotations.
     *  
     * @param  NameTreeType  $ap
     * @return NameDictionaryType
     */
    public function setAP(NameTreeType $ap): NameDictionaryType
    {
        $this->setEntry('AP', $ap);
        return $this;
    }

    /**
     * Set mapping to document-level Javascript actions.
     *  
     * @param  NameTreeType  $javascript
     * @return NameDictionaryType
     */
    public function setJavaScript(NameTreeType $javascript): NameDictionaryType
    {
        $this->setEntry('JavaScript', $javascript);
        return $this;
    }


    /**
     * Set mapping to pages.
     *  
     * @param  NameTreeType  $pages
     * @return NameDictionaryType
     */
    public function setPages(NameTreeType $pages): NameDictionaryType
    {
        $this->setEntry('Pages', $pages);
        return $this;
    }


    /**
     * Set mapping to (invisible) pages.
     *  
     * @param  NameTreeType  $templates
     * @return NameDictionaryType
     */
    public function setTemplates(NameTreeType $templates): NameDictionaryType
    {
        $this->setEntry('Templates', $templates);
        return $this;
    }

    /**
     * Set IDS mapping to Web Capture content sets.
     *  
     * @param  NameTreeType  $ids
     * @return NameDictionaryType
     */
    public function setIDS(NameTreeType $ids): NameDictionaryType
    {
        $this->setEntry('IDS', $ids);
        return $this;
    }

    /**
     * Set URLS mapping to Web Capture content sets.
     *  
     * @param  NameTreeType  $urls
     * @return NameDictionaryType
     */
    public function setURLS(NameTreeType $urls): NameDictionaryType
    {
        $this->setEntry('URLS', $urls);
        return $this;
    }


    /**
     * Set mapping to file specifications for embedded file streams.
     *  
     * @param  NameTreeType  $files
     * @return NameDictionaryType
     */
    public function setEmbeddedFiles(NameTreeType $files): NameDictionaryType
    {
        $this->setEntry('EmbeddedFiles', $files);
        return $this;
    }

    /**
     * Set mapping to alternate presentations.
     *  
     * @param  NameTreeType  $presentations
     * @return NameDictionaryType
     */
    public function setAlternatePresentations(NameTreeType $presentations): NameDictionaryType
    {
        $this->setEntry('AlternatePresentations', $presentations);
        return $this;
    }

    /**
     * Set mapping to renditions objects.
     *  
     * @param  NameTreeType  $renditions
     * @return NameDictionaryType
     */
    public function setRenditions(NameTreeType $renditions): NameDictionaryType
    {
        $this->setEntry('Renditions', $renditions);
        return $this;
    }
}