<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;

class PageTreeDictionaryType extends DictionaryType
{ 
    /**
     * Get node.
     *  
     * @return PageTreeNodeDictionaryType
     */
    public function getNode(): PageTreeNodeDictionaryType
    {
        if (!$this->hasEntry('Node')) {
            $node = Factory::create('Papier\Type\PageTreeNodeDictionaryType', null, true);
            $this->setEntry('Node', $node);
        }

		/** @var PageTreeNodeDictionaryType $node */
		$node = $this->getEntry('Node');
        return $node;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $node = $this->getNode();
        return $node->format();
    }
}