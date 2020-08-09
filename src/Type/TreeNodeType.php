<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\LiteralStringKeyArrayObject;
use Papier\Object\LiteralStringObject;
use Papier\Object\LimitsArrayObject;

use Papier\Validator\BoolValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RunTimeException;

class TreeNodeType extends DictionaryObject
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
        if (!BoolValidator::isValid($root)) {
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
     * @return \Papier\Object\ArrayObject
     */
    protected function getKids()
    {
        if ($this->hasKey('Names')) {
            throw new RunTimeException("Names is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasKey('Kids')) {
            $kids = new ArrayObject();
            $this->setObjectForKey('Kids', $kids);
        }

        return $this->getObjectForKey('Kids');
    }

    
    /**
     * Add kid to node.
     *  
     * @return \Papier\Type\TreeNodeType
     */
    public function addKid()
    {
        $node = Factory::getInstance()->createType('TreeNode');
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

        if ($this->hasKey('Names')) {
            throw new RunTimeException("Names is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setObjectForKey('Kids', $kids);
    }

    /**
     * Set names.
     *  
     * @param  \Papier\Object\LiteralStringKeyArrayObject  $names
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    protected function setNames($names)
    {
        if (!$names instanceof LiteralStringKeyArrayObject) {
            throw new InvalidArgumentException("Names is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasKey('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setObjectForKey('Names', $names);
    } 

    /**
     * Get names.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Object\LiteralStringKeyArrayObject
     */
    protected function getNames()
    {
        if ($this->hasKey('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasKey('Names')) {
            $names = new LiteralStringKeyArrayObject();
            $this->setObjectForKey('Names', $names);
        }

        return $this->getObjectForKey('Names');
    }

    /**
     * Set limits.
     *  
     * @param  \Papier\Object\LimitsArrayObject  $limits
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    protected function setLimits($limits)
    {
        if (!$limits instanceof LimitsArrayObject) {
            throw new InvalidArgumentException("Limits is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return $this->setObjectForKey('Limits', $limits);
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

        if ($node->hasKey('Names')) {
            $names = $node->getObjectForKey('Names')->getKeys();
            $objects = array_merge($objects, $names);
        } else {
            $kids = $node->getObjectForKey('Kids');
            
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