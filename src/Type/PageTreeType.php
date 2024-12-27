<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;

class PageTreeType extends DictionaryType
{ 
    /**
     * Get node.
     *  
     * @return PageTreeNodeType
     */
    public function getNode(): PageTreeNodeType
    {
        if (!$this->hasEntry('Node')) {
            $node = Factory::create('Papier\Type\PageTreeNodeType', null, true);
            $this->setEntry('Node', $node);
        }

		/** @var PageTreeNodeType $node */
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