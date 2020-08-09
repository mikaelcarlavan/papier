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
        if (!$this->hasKey('Root')) {
            $root = Factory::getInstance()->createType('NumberTreeNode')->setRoot();
            $this->setObjectForKey('Root', $root);
        }

        return $this->getObjectForKey('Root');
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