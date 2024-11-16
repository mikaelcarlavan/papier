<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Factory\Factory;

use RunTimeException;

class TreeNodeType extends DictionaryType
{ 
    /**
     * Define node as root.
     *
     * @var bool
     */
    protected bool $root = false;

    /**
     * Set node to be root.
     *  
     * @param bool $root
     * @return TreeNodeType
     */
    public function setRoot(bool $root = true): TreeNodeType
    {
        $this->root = $root;
        return $this;
    } 

    /**
     * Returns if node is root.
     *  
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->root;
    } 

    /**
     * Get kids.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return ArrayType
     */
    protected function getKids(): ArrayType
    {
        if ($this->hasEntry('Names')) {
            throw new RunTimeException("Names is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasEntry('Kids')) {
            $kids = Factory::create('Papier\Type\ArrayType');
            $this->setEntry('Kids', $kids);
        }

        return $this->getEntry('Kids');
    }

    
    /**
     * Add kid to node.
     *  
     * @return TreeNodeType
     */
    public function addKid(): TreeNodeType
    {
        $node = Factory::create('Papier\Type\TreeNodeType', null, true);
        $this->getKids()->append($node);

        return $node;
    }
    
    /**
     * Set kids.
     *  
     * @param  ArrayObject  $kids
     * @throws RunTimeException if node already contains 'Names' key.
     * @return TreeNodeType
     */
    protected function setKids(ArrayObject $kids): TreeNodeType
    {
        if ($this->hasEntry('Names')) {
            throw new RunTimeException("Names is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

		$this->setEntry('Kids', $kids);
        return $this;
    }

    /**
     * Set names.
     *  
     * @param  LiteralStringKeyArrayType  $names
     * @throws RunTimeException if node already contains 'Names' key.
     * @return TreeNodeType
     */
    protected function setNames(LiteralStringKeyArrayType $names): TreeNodeType
    {
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

		$this->setEntry('Names', $names);
        return $this;
    } 

    /**
     * Get names.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return LiteralStringKeyArrayType
     */
    protected function getNames(): LiteralStringKeyArrayType
    {
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasEntry('Names')) {
            $names = Factory::create('Papier\Type\LiteralStringKeyArrayType');
            $this->setEntry('Names', $names);
        }

        return $this->getEntry('Names');
    }

    /**
     * Set limits.
     *  
     * @param  LimitsArrayType  $limits
     * @return TreeNodeType
     */
    protected function setLimits(LimitsArrayType $limits): TreeNodeType
    {
		$this->setEntry('Limits', $limits);
        return $this;
    } 


    /**
     * Get names from node
     *
     * @param  TreeNodeType  $node
     * @return array
     */    
    protected function collectNames(TreeNodeType $node): array
    {
        $objects = array();

        if ($node->hasEntry('Names')) {
            $names = $node->getEntry('Names')->getKeys();
            $objects = array_merge($objects, $names);
        } else {
            $kids = $node->getEntry('Kids');
            
            if (count($kids) > 0) {
                foreach ($kids as $kid) {
                    $names = $this->collectNames($kid);
                    $objects = array_merge($objects, $names);
                }
            }
        }

        return $objects;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $value = $this->getValue();    
        asort($value);
        $this->setValue($value);
        
        return parent::format();
    }
}