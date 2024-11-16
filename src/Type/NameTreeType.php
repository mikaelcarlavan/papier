<?php

namespace Papier\Type;

use Papier\Factory\Factory;

class NameTreeType extends TreeType
{ 
    /**
     * Get root.
     *  
     * @return NameTreeNodeType
     */
    public function getRoot(): NameTreeNodeType
    {
        if (!$this->hasEntry('Root')) {
			/** @var NameTreeNodeType $root */
            $root = Factory::create('Papier\Type\NameTreeNodeType', null, true);
			$root->setRoot();
            $this->setEntry('Root', $root);
        }

        return $this->getEntry('Root');
    }
    
    /**
     * Add number to tree.
     *
     * @param  string  $key
     * @param  mixed  $object
     * @return NameTreeType
     */
    public function addName(string $key, $object): NameTreeType
    {
        $this->getRoot()->addName($key, $object);
        return $this;
    }
}