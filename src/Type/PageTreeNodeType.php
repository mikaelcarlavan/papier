<?php

namespace Papier\Type;

use Papier\Type\PageObjectType;
use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class PageTreeNodeType extends DictionaryObject
{
    /**
     * Set parent.
     *  
     * @param  \Papier\Type\PageTreeNodeType  $parent
     * @throws InvalidArgumentException if the provided argument is not of type 'PageTreeNodeType'.
     * @return \Papier\Type\PageTreeNodeType
     */
    public function setParent($parent)
    {
        if (!$parent instanceof PageTreeNodeType) {
            throw new InvalidArgumentException("Parent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setObjectForKey('Parent', $parent);
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return \Papier\Type\PageTreeNodeType
     */
    public function getParent()
    {
        return $this->getObjectForKey('Parent');
    } 

    /**
     * Get kids.
     *  
     * @return \Papier\Object\ArrayObject
     */
    protected function getKids()
    {
        if (!$this->hasKey('Kids')) {
            $kids = new ArrayObject();
            $this->setObjectForKey('Kids', $kids);
        }

        return $this->getObjectForKey('Kids');
    }

    
    /**
     * Add node to tree.
     *  
     * @return \Papier\Type\PageTreeNode
     */
    public function addNode()
    {
        $node = Factory::getInstance()->createType('PageTreeNode');
        $this->getKids()->append($node);

        return $node;
    }
   
    /**
     * Add object to node.
     *  
     * @return \Papier\Type\PageObjectType
     */
    public function addObject()
    {
        $node = Factory::getInstance()->createType('PageObject');
        $this->getKids()->append($node);

        return $node;
    }

    /**
     * Set kids.
     *  
     * @param  \Papier\Object\ArrayObject  $kids
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\TreeNodeType
     */
    protected function setKids($kids)
    {
        if (!$kids instanceof ArrayObject) {
            throw new InvalidArgumentException("Kids is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return $this->setObjectForKey('Kids', $kids);
    }

    /**
     * Format catalog's content.
     *
     * @return string
     */
    public function format()
    {
        $parent = $this->getParent();

        if (!$parent instanceof PageTreeNodeType) {
            throw new RuntimeException("Parent is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $kids = $this->getKids();

        $num = count($kids);

        if ($num > 0) {
            $kid = $kids->first();

            $allObjects = $kid instanceof PageObjectType;
            $allNodes = $kid instanceof PageTreeNodeType;

            foreach ($kids as $kid) {
                if ($kid instanceof PageTreeNodeType && $allObjects) {
                    throw new RuntimeException("All kids should be of same type. See ".__CLASS__." class's documentation for possible values.");  
                }

                if ($kid instanceof PageObjectType && $allNodes) {
                    throw new RuntimeException("All kids should be of same type. See ".__CLASS__." class's documentation for possible values.");  
                }
            }
        }
        
        $type = Factory::getInstance()->createObject('Name')->setIndirect(false);
        $type->setValue('Pages');

        $count = Factory::getInstance()->createObject('Integer')->setIndirect(false);
        $count->setValue($num);

        $this->setObjectForKey('Type', $type);
        $this->setObjectForKey('Count', $count);
        
        return parent::format();
    }
}