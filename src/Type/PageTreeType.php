<?php

namespace Papier\Type;

use Papier\Type\DictionaryType;
use Papier\Factory\Factory;

class PageTreeType extends DictionaryType
{ 
    /**
     * Get node.
     *  
     * @return \Papier\Type\PageTreeNodeType
     */
    public function getNode()
    {
        if (!$this->hasEntry('Node')) {
            $node = Factory::create('PageTreeNode');
            $this->setEntry('Node', $node);
        }

        return $this->getEntry('Node');
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $node = $this->getNode();
        return $node->format();
    }
}