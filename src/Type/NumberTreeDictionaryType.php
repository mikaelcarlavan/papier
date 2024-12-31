<?php

namespace Papier\Type;

use Papier\Type\TreeDictionaryType;
use Papier\Factory\Factory;

class NumberTreeDictionaryType extends TreeDictionaryType
{ 
    /**
     * Get root.
     *  
     * @return NumberTreeNodeDictionaryType
     */
    public function getRoot(): NumberTreeNodeDictionaryType
    {
        if (!$this->hasEntry('Root')) {
            $root = Factory::create('Papier\Type\NumberTreeNodeDictionaryType', null, true)->setRoot();
            $this->setEntry('Root', $root);
        }

		/** @var NumberTreeNodeDictionaryType $root */
		$root = $this->getEntry('Root');
        return $root;
    }

    /**
     * Add number to tree.
     *  
     * @param  mixed  $object
     * @param string $key
     * @return NumberTreeDictionaryType
     */
    public function addNum(string $key, $object): NumberTreeDictionaryType
    {
        $this->getRoot()->addNum($key, $object);
        return $this;
    }
}