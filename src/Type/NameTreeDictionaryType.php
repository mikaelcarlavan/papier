<?php

namespace Papier\Type;

use Papier\Factory\Factory;

class NameTreeDictionaryType extends TreeDictionaryType
{ 
    /**
     * Get root.
     *  
     * @return NameTreeNodeDictionaryType
     */
    public function getRoot(): NameTreeNodeDictionaryType
    {
        if (!$this->hasEntry('Root')) {
			/** @var NameTreeNodeDictionaryType $root */
            $root = Factory::create('Papier\Type\NameTreeNodeDictionaryType', null, true);
			$root->setRoot();
            $this->setEntry('Root', $root);
        }

		/** @var NameTreeNodeDictionaryType $root */
		$root = $this->getEntry('Root');
        return $root;
    }
    
    /**
     * Add number to tree.
     *
     * @param  string  $key
     * @param  mixed  $object
     * @return NameTreeDictionaryType
     */
    public function addName(string $key, $object): NameTreeDictionaryType
    {
        $this->getRoot()->addName($key, $object);
        return $this;
    }
}