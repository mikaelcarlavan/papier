<?php

namespace Papier\Type;

use Papier\Factory\Factory;

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

        return $this->getEntry('Node');
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