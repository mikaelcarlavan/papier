<?php

namespace Papier\Object;

use Papier\Object\DictionaryObject;
use Papier\Type\NameTreeType;

use InvalidArgumentException;

class NameDictionaryObject extends DictionaryObject
{
    /**
     * Set mapping to destinations.
     *  
     * @param  \Papier\Type\NameTreeType  $dests
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setDests($dests)
    {
        if (!$dests instanceof NameTreeType) {
            throw new InvalidArgumentException("Dests is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Dests', $dests);
        return $this;
    }

    /**
     * Set mapping to annotations.
     *  
     * @param  \Papier\Type\NameTreeType  $ap
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setAP($ap)
    {
        if (!$ap instanceof NameTreeType) {
            throw new InvalidArgumentException("AP is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('AP', $ap);
        return $this;
    }

    /**
     * Set mapping to document-level Javascript actions.
     *  
     * @param  \Papier\Type\NameTreeType  $javascript
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setJavaScript($javascript)
    {
        if (!$javascript instanceof NameTreeType) {
            throw new InvalidArgumentException("JavaScript is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('JavaScript', $javascript);
        return $this;
    }


    /**
     * Set mapping to pages.
     *  
     * @param  \Papier\Type\NameTreeType  $pages
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setPages($pages)
    {
        if (!$pages instanceof NameTreeType) {
            throw new InvalidArgumentException("Pages is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Pages', $pages);
        return $this;
    }


    /**
     * Set mapping to (invisible) pages.
     *  
     * @param  \Papier\Type\NameTreeType  $templates
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setTemplates($templates)
    {
        if (!$templates instanceof NameTreeType) {
            throw new InvalidArgumentException("Templates is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Templates', $templates);
        return $this;
    }

    /**
     * Set IDS mapping to Web Capture content sets.
     *  
     * @param  \Papier\Type\NameTreeType  $ids
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setIDS($ids)
    {
        if (!$ids instanceof NameTreeType) {
            throw new InvalidArgumentException("IDS is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('IDS', $ids);
        return $this;
    }

    /**
     * Set URLS mapping to Web Capture content sets.
     *  
     * @param  \Papier\Type\NameTreeType  $urls
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setURLS($urls)
    {
        if (!$urls instanceof NameTreeType) {
            throw new InvalidArgumentException("URLS is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('URLS', $urls);
        return $this;
    }


    /**
     * Set mapping to file specifications for embedded file streams.
     *  
     * @param  \Papier\Type\NameTreeType  $files
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setEmbeddedFiles($files)
    {
        if (!$files instanceof NameTreeType) {
            throw new InvalidArgumentException("EmbeddedFiles is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('EmbeddedFiles', $files);
        return $this;
    }

    /**
     * Set mapping to alternate presentations.
     *  
     * @param  \Papier\Type\NameTreeType  $presentations
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setAlternatePresentations($presentations)
    {
        if (!$presentations instanceof NameTreeType) {
            throw new InvalidArgumentException("AlternatePresentations is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('AlternatePresentations', $presentations);
        return $this;
    }

    /**
     * Set mapping to renditions objects.
     *  
     * @param  \Papier\Type\NameTreeType  $renditions
     * @throws InvalidArgumentException if the provided argument is not of type 'NameTreeType'.
     * @return \Papier\Object\NameDictionaryObject
     */
    public function setRenditions($renditions)
    {
        if (!$renditions instanceof NameTreeType) {
            throw new InvalidArgumentException("Renditions is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->addEntry('Renditions', $renditions);
        return $this;
    }
}