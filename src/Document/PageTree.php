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
        if (!$this->hasKey('Node')) {
            $node = Factory::getInstance()->createType('PageTreeNode');
            $this->setObjectForKey('Node', $node);
        }

        return $this->getObjectForKey('Node');
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