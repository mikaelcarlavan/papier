<?php

namespace Papier\Type;

use Papier\Type\TreeType;
use Papier\Factory\Factory;

class NumberTreeType extends TreeType
{ 
    /**
     * Get root.
     *  
     * @return \Papier\Type\NumberTreeNodeType
     */
    public function getRoot()
    {
        if (!$this->hasEntry('Root')) {
            $root = Factory::create('NumberTreeNode', null, true)->setRoot();
            $this->setEntry('Root', $root);
        }

        return $this->getEntry('Root');
    }

    /**
     * Add number to tree.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @return \Papier\Type\NumberTreeType
     */
    public function addNum($key, $object)
    {
        $this->getRoot()->addNum($key, $object);
        return $this;
    }
}