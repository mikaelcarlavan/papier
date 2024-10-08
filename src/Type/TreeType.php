<?php

namespace Papier\Type;

use Papier\Factory\Factory;

class TreeType extends DictionaryType
{ 
    /**
     * Get root.
     *  
     * @return TreeNodeType
     */
    public function getRoot(): TreeNodeType
    {
        if (!$this->hasEntry('Root')) {
            $root = Factory::create('TreeNode', null, true)->setRoot();
            $this->setEntry('Root', $root);
        }

        return $this->getEntry('Root');
    }
    
    /**
     * Add kid to tree.
     *  
     * @return TreeNodeType
     */
    public function addKid(): TreeNodeType
    {
        return $this->getRoot()->addKid();
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $root = $this->getRoot();
        return $root->format();
    }
}