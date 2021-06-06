<?php

namespace Papier\Type;

use Papier\Type\TreeType;
use Papier\Factory\Factory;

class NumberTreeType extends TreeType
{ 
    /**
     * Get root.
     *  
     * @return NumberTreeNodeType
     */
    public function getRoot(): NumberTreeNodeType
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
     * @param string $key
     * @return NumberTreeType
     */
    public function addNum(string $key, $object): NumberTreeType
    {
        $this->getRoot()->addNum($key, $object);
        return $this;
    }
}