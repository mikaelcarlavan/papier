<?php

namespace Papier\Type;

use Papier\Document\PageTree\PageTreeObject;
use Papier\Object\DictionaryObject;

use InvalidArgumentException;
use RuntimeException;

class PageTreeNodeType extends ArrayObject
{

    /**
     * Parent of tree node.
     *
     * @var \Papier\Object\DictionaryObject
     */
    private $parent = null;

    /**
     * Set parent.
     *  
     * @param  \Papier\Object\DictionaryObject  $parent
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Document\PageTree\PageTreeNode
     */
    public function setParent($parent)
    {
        if (!$parent instanceof DictionaryObject) {
            throw new InvalidArgumentException("Parent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->parent = $parent;
        return $this;
    } 

     /**
     * Get parent.
     *  
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    } 

    /**
     * Add new page tree object.
     *  
     * @return \Document\PageTree\PageTreeObject
     */
    public function addObject()
    {
        $object = new PageTreeObject();
        $object->setIndirect(true);

        $this->append($object);

        return $object;
    }

    /**
     * Format catalog's content.
     *
     * @return string
     */
    public function format()
    {
        $parent = $this->getParent();
        $objects = $this->getObjects();

        $type = new NameObject();
        $type->setValue('Pages');

        $num = new IntegerObject();
        $num->setValue(count($objects));

        if (!$parent) {
            throw new RuntimeException("Parent is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $dictionary = new DictionaryObject();
        $dictionary->setObjectForKey('Type', $type);
        $dictionary->setObjectForKey('Parent', $parent);
        $dictionary->setObjectForKey('Kids', $objects);
        $dictionary->setObjectForKey('Count', $num);
        
        $value = $dictionary->write();
        return $value;
    }
}