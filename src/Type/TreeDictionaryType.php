<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;

class TreeDictionaryType extends DictionaryType
{ 
    /**
     * Get root.
     *  
     * @return TreeNodeDictionaryType
     */
    public function getRoot(): TreeNodeDictionaryType
    {
        if (!$this->hasEntry('Root')) {
			/** @var TreeNodeDictionaryType $root */
            $root = Factory::create('Papier\Type\TreeNodeDictionaryType', null, true);
			$root->setRoot();
            $this->setEntry('Root', $root);
        }

		/** @var TreeNodeDictionaryType $root */
		$root = $this->getEntry('Root');
        return $root;
    }
    
    /**
     * Add kid to tree.
     *  
     * @return TreeNodeDictionaryType
     */
    public function addKid(): TreeNodeDictionaryType
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