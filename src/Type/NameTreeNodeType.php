<?php

namespace Papier\Type;

use Papier\Factory\Factory;

use RunTimeException;

class NameTreeNodeType extends TreeNodeType
{ 
    /**
     * Add kid to node.
     *  
     * @return NameTreeNodeType
     */
    public function addKid(): NameTreeNodeType
    {
        $node = Factory::create('Papier\Type\NameTreeNodeType');
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Add name to node.
     *  
     * @param  mixed  $object
     * @param string $key
     * @return NameTreeNodeType
     */
    public function addName(string $key, $object): NameTreeNodeType
    {
        $this->getNames()->setEntry($key, $object);
        return $this;
    }

    /**
     * Get kids.
     *  
     * @throws RunTimeException if node already contains 'Kids' key.
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

		/** @var LiteralStringKeyArrayType $names */
		$names = $this->getEntry('Names');
        return $names;
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
            $limits = Factory::create('Papier\Type\LimitsArrayType');
            $objects = $this->collectNames($this);

            if (count($objects)) {
                sort($objects);

                $first = Factory::create('Papier\Type\LiteralStringType', array_shift($objects));
                $last = Factory::create('Papier\Type\LiteralStringType', array_pop($objects));
                
                $limits->append($first);
                $limits->append($last);
        
                $this->setLimits($limits);
            }
        }
        
        return parent::format();
    }
}