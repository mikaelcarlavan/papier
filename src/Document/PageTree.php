<?php

namespace Papier\Document;

use Papier\Object\DictionaryObject;
use Papier\Factory\Factory;

class PageTree extends DictionaryObject
{ 
    /**
     * Get node.
     *  
     * @return \Papier\Type\PageTreeNodeType
     */
    public function getNode()
    {
        if (!$this->hasEntry('Node')) {
            $node = Factory::getInstance()->createType('PageTreeNode');
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