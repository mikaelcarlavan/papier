<?php

namespace Papier\Document\PageTree;

use Papier\Document\PageTree\PageTreeObject;
use Papier\Object\DictionaryObject;

use InvalidArgumentException;
use RuntimeException;

class PageTreeNode extends ArrayObject
{

    /**
     * Parent of tree node.
     *
     * @var \Papier\Object\DictionaryObject
     */
    private $parent = null;

    /**
     * Add object to page tree.
     *  
     * @param  \Papier\Document\PageTree\PageTreeObject  $object
     * @return \Papier\Document\PageTree\PageTreeNode
     */
    private function addObject($object)
    {
        if (!$parent instanceof PageTreeObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        return parent::addObject($object);
    }

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
            throw new InvalidArgumentException("Parent is incorrect. See ".get_class($this)." class's documentation for possible values.");
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
    public function addPageTreeObject()
    {
        $object = new PageTreeObject();
        $object->setIndirect(true);

        $this->addObject($object);

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
            throw new RuntimeException("Parent is missing. See ".get_class($this)." class's documentation for possible values.");
        }

        $dictionary = new Dictionary();
        $dictionary->addEntry('Type', $type);
        $dictionary->addEntry('Parent', $parent);
        $dictionary->addEntry('Kids', $objects);
        $dictionary->addEntry('Count', $num);
        
        $value = $dictionary->write();
        return $value;
    }
}