<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Factory\Factory;

class TreeType extends DictionaryObject
{ 
    /**
     * Get root.
     *  
     * @return \Papier\Type\TreeNodeType
     */
    public function getRoot()
    {
        if (!$this->hasEntry('Root')) {
            $root = Factory::getInstance()->createType('TreeNode')->setRoot();
            $this->setEntry('Root', $root);
        }

        return $this->getEntry('Root');
    }
    
    /**
     * Add kid to tree.
     *  
     * @return \Papier\Type\TreeNodeType
     */
    public function addKid()
    {
        $node = $this->getRoot()->addKid();
        return $node;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $root = $this->getRoot();
        return $root->format();
    }
}