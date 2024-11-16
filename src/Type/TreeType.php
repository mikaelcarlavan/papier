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
			/** @var TreeNodeType $root */
            $root = Factory::create('Papier\Type\TreeNodeType', null, true);
			$root->setRoot();
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