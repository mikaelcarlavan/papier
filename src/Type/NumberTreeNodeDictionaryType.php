<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\ArrayType;
use RunTimeException;

class NumberTreeNodeDictionaryType extends TreeNodeDictionaryType
{  
    /**
     * Add kid to node.
     *  
     * @return NumberTreeNodeDictionaryType
     */
    public function addKid(): NumberTreeNodeDictionaryType
    {
		/** @var NumberTreeNodeDictionaryType $node */
		$node = Factory::create('Papier\Type\NumberTreeNodeDictionaryType', null, true);
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Add number to node.
     *  
     * @param  mixed  $object
     * @param string $key
     * @return NumberTreeNodeDictionaryType
     */
    public function addNum(string $key, $object): NumberTreeNodeDictionaryType
    {
        $this->getNums()->setEntry($key, $object);
        return $this;
    }

    /**
     * Set nums.
     *  
     * @param  IntegerKeyArrayType  $nums
     * @return NumberTreeNodeDictionaryType
     *@throws RunTimeException if node already contains 'Kids' key.
     */
    protected function setNums(IntegerKeyArrayType $nums): NumberTreeNodeDictionaryType
	{
        if ($this->hasEntry('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

		$this->setEntry('Nums', $nums);
        return $this;
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
            $nums = Factory::create('Papier\Type\IntegerKeyArrayType');
            $this->setEntry('Nums', $nums);
        }

		/** @var IntegerKeyArrayType $nums */
		$nums = $this->getEntry('Nums');
        return $nums;
    }

    /**
     * Get nums from node.
     *
     * @param  TreeNodeDictionaryType $node
     * @return array<mixed>
     */    
    protected function collectNums(TreeNodeDictionaryType $node): array
    {
        $objects = array();

        if ($node->hasEntry('Nums')) {
			/** @var IntegerKeyArrayType $nums */
            $nums = $node->getEntry('Nums');
			/** @var array<mixed> $keys */
			$keys = $nums->getKeys();
			$objects = array_merge($objects, $keys);
        } else {
			/** @var ArrayType $kids */
            $kids = $node->getEntry('Kids');
            if (count($kids) > 0) {
                foreach ($kids as $kid) {
					/** @var TreeNodeDictionaryType $kid */
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
			/** @var LimitsArrayType $limits */
			$limits = Factory::create('Papier\Type\LimitsArrayType');
            $objects = $this->collectNums($this);

            if (count($objects)) {
                sort($objects);

                $first = Factory::create('Papier\Type\Base\IntegerType', array_shift($objects));
                $last = Factory::create('Papier\Type\Base\IntegerType', array_pop($objects));
                
                $limits->append($first);
                $limits->append($last);
        
                $this->setLimits($limits);
            }
        }
                
        return parent::format();
    }
}