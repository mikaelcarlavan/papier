<?php

namespace Papier\Type;

use Papier\Type\TreeNodeType;
use Papier\Object\IntegerKeyArrayObject;
use Papier\Object\IntegerObject;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RunTimeException;

class NumberTreeNodeType extends TreeNodeType
{  
    /**
     * Add kid to node.
     *  
     * @return \Papier\Type\NumberTreeNodeType
     */
    public function addKid()
    {
        $node = Factory::getInstance()->createType('NumberTreeNode');
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Add number to node.
     *  
     * @param  mixed  $object
     * @param  string  $key
     * @return \Papier\Type\NumberTreeNodeType
     */
    public function addNum($key, $object)
    {
        $this->getNums()->setEntry($key, $object);
        return $this;
    }

    /**
     * Set nums.
     *  
     * @param  \Papier\Object\IntegerKeyArrayObject  $nums
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    protected function setNums($nums)
    {
        if (!$nums instanceof IntegerKeyArrayObject) {
            throw new InvalidArgumentException("Nums is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setEntry('Nums', $nums);
    } 

    /**
     * Get nums.
     *  
     * @throws RunTimeException if node already contains 'Kids' key.
     * @return \Papier\Object\IntegerKeyArrayObject
     */
    protected function getNums()
    {
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasEntry('Nums')) {
            $nums = Factory::getInstance()->createObject('IntegerKeyArray', null, false);
            $this->setEntry('Nums', $nums);
        }

        return $this->getEntry('Nums');
    }

    /**
     * Get nums from node.
     *
     * @param  \Papier\Type\TreeNodeType  $node
     * @return array
     */    
    protected function collectNums($node)
    {        
        if (!$node instanceof TreeNodeType) {
            throw new InvalidArgumentException("Node is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }  

        $objects = array();

        if ($node->hasEntry('Nums')) {
            $nums = $node->getEntry('Nums')->getKeys();
            $objects = array_merge($objects, $nums);
        } else {
            $kids = $node->getEntry('Kids');
            
            if (count($kids) > 0) {
                foreach ($kids as $kid) {
                    $nums = $this->collectNums($kid);
                    $objects = array_merge($objects, $nums);
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
        if (!$this->isRoot()) {
            // Compute limits
            $limits = Factory::getInstance()->createType('LimitsArray', null, false);

            $objects = $this->collectNums($this);

            if (count($objects)) {
                sort($objects);

                $first = Factory::getInstance()->createObject('Integer', array_shift($objects), false);
                $last = Factory::getInstance()->createObject('Integer', array_pop($objects), false);
                
                $limits->append($first);
                $limits->append($last);
        
                $this->setLimits($limits);
            }
        }
                
        return parent::format();
    }
}