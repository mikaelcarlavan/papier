<?php

namespace Papier\Type;

use Papier\Type\TreeType;
use Papier\Factory\Factory;

class NameTreeType extends TreeType
{ 
    /**
     * Get root.
     *  
     * @return \Papier\Type\NameTreeNodeType
     */
    public function getRoot()
    {
        if (!$this->hasEntry('Root')) {
            $root = Factory::create('NameTreeNode')->setRoot();
            $this->setEntry('Root', $root);
        }

        return $this->getEntry('Root');
    }
    
    /**
     * Add number to tree.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @return \Papier\Type\NameTreeType
     */
    public function addName($key, $object)
    {
        $this->getRoot()->addName($key, $object);
        return $this;
    }
}