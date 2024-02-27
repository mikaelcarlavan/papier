<?php

namespace Papier\Type;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RunTimeException;

class NumberTreeNodeType extends TreeNodeType
{  
    /**
     * Add kid to node.
     *  
     * @return NumberTreeNodeType
     */
    public function addKid(): NumberTreeNodeType
    {
        $node = Factory::create('NumberTreeNode', null, true);
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Add number to node.
     *  
     * @param  mixed  $object
     * @param string $key
     * @return NumberTreeNodeType
     */
    public function addNum(string $key, $object): NumberTreeNodeType
    {
        $this->getNums()->setEntry($key, $object);
        return $this;
    }

    /**
     * Set nums.
     *  
     * @param  IntegerKeyArrayType  $nums
     * @throws RunTimeException if node already contains 'Kids' key.
     * @return NumberTreeNodeType
     */
    protected function setNums(IntegerKeyArrayType $nums): NumberTreeNodeType
    {
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setEntry('Nums', $nums);
    } 

    /**
     * Get nums.
     *  
     * @throws RunTimeException if node already contains 'Kids' key.
     * @return IntegerKeyArrayType
     */
    protected function getNums(): IntegerKeyArrayType
    {
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        if (!$this->hasEntry('Nums')) {
            $nums = Factory::create('IntegerKeyArray');
            $this->setEntry('Nums', $nums);
        }

        return $this->getEntry('Nums');
    }

    /**
     * Get nums from node.
     *
     * @param  TreeNodeType  $node
     * @return array
     */    
    protected function collectNums(TreeNodeType $node): array
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
    public function format(): string
    {
        if (!$this->isRoot()) {
            // Compute limits
            $limits = Factory::create('LimitsArray');

            $objects = $this->collectNums($this);

            if (count($objects)) {
                sort($objects);

                $first = Factory::create('Papier\Type\IntegerType', array_shift($objects));
                $last = Factory::create('Papier\Type\IntegerType', array_pop($objects));
                
                $limits->append($first);
                $limits->append($last);
        
                $this->setLimits($limits);
            }
        }
                
        return parent::format();
    }
}