<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Type\LiteralStringKeyArrayType;
use Papier\Type\LiteralStringType;

use Papier\Validator\BooleanValidator;

use Papier\Factory\Factory;

use Papier\Type\DictionaryType;
use Papier\Type\LimitsArrayType;


use InvalidArgumentException;
use RunTimeException;

class TreeNodeType extends DictionaryType
{ 
    /**
     * Define node as root.
     *
     * @var bool
     */
    protected $root = false;

    /**
     * Set node to be root.
     *  
     * @param  bool  $root
     * @return \Papier\Type\TreeNodeType
     */
    public function setRoot($root = true)
    {
        if (!BooleanValidator::isValid($root)) {
            throw new InvalidArgumentException("Root boolean is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->root = $root;
        return $this;
    } 

    /**
     * Returns if node is root.
     *  
     * @return bool
     */
    public function isRoot()
    {
        return $this->root;
    } 

    /**
     * Get kids.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Type\ArrayType
     */
    protected function getKids()
    {
        if ($this->hasEntry('Names')) {
            throw new RunTimeException("Names is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasEntry('Kids')) {
            $kids = Factory::create('Array');
            $this->setEntry('Kids', $kids);
        }

        return $this->getEntry('Kids');
    }

    
    /**
     * Add kid to node.
     *  
     * @return \Papier\Type\TreeNodeType
     */
    public function addKid()
    {
        $node = Factory::create('TreeNode', null, true);
        $this->getKids()->append($node);

        return $node;
    }
    
    /**
     * Set kids.
     *  
     * @param  \Papier\Object\ArrayObject  $kids
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    protected function setKids($kids)
    {
        if (!$kids instanceof ArrayObject) {
            throw new InvalidArgumentException("Kids is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Names')) {
            throw new RunTimeException("Names is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setEntry('Kids', $kids);
    }

    /**
     * Set names.
     *  
     * @param  \Papier\Type\LiteralStringKeyArrayType  $names
     * @throws InvalidArgumentException if the provided argument is not of type 'LiteralStringKeyArrayType'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    protected function setNames($names)
    {
        if (!$names instanceof LiteralStringKeyArrayType) {
            throw new InvalidArgumentException("Names is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setEntry('Names', $names);
    } 

    /**
     * Get names.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Type\LiteralStringKeyArrayType
     */
    protected function getNames()
    {
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasEntry('Names')) {
            $names = Factory::create('LiteralStringKeyArray');
            $this->setEntry('Names', $names);
        }

        return $this->getEntry('Names');
    }

    /**
     * Set limits.
     *  
     * @param  \Papier\Type\LimitsArrayType  $limits
     * @throws InvalidArgumentException if the provided argument is not of type 'LimitsArrayType'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    protected function setLimits($limits)
    {
        if (!$limits instanceof LimitsArrayType) {
            throw new InvalidArgumentException("Limits is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return $this->setEntry('Limits', $limits);
    } 


    /**
     * Get names from node
     *
     * @param  \Papier\Type\TreeNodeType  $node
     * @return array
     */    
    protected function collectNames($node)
    {        
        if (!$node instanceof TreeNodeType) {
            throw new InvalidArgumentException("Node is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }  

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
    public function format()
    {
        $value = $this->getValue();    
        asort($value);
        $this->setValue($value);
        
        return parent::format();
    }
}