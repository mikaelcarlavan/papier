<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use InvalidArgumentException;
use RunTimeException;

class TreeNodeType extends DictionaryObject
{

    /**
     * Add entry to tree node's dictionnary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Document\DocumentCatalog
     */
    private function addEntry($key, $object)
    {
        $this->setObjectForKey($key, $object);
        return $this;
    } 
 
    /**
     * Set kids.
     *  
     * @param  \Papier\Object\ArrayObject  $kids
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setKids($kids)
    {
        if (!$kids instanceof ArrayObject) {
            throw new InvalidArgumentException("Kids is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        if ($this->hasKey('Names')) {
            throw new RunTimeException("Names is already present. See ".get_class($this)." class's documentation for possible values.");  
        }

        return $this->addEntry('Kids', $kids);
    }
    
    /**
     * Set names.
     *  
     * @param  \Papier\Object\ArrayObject  $names
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setNames($names)
    {
        if (!$names instanceof ArrayObject) {
            throw new InvalidArgumentException("Names is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        if ($this->hasKey('Kids')) {
            throw new RunTimeException("Kids is already present. See ".get_class($this)." class's documentation for possible values.");  
        }

        return $this->addEntry('Names', $names);
    } 

    /**
     * Set limits.
     *  
     * @param  \Papier\Object\ArrayObject  $limits
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setLimits($limits)
    {
        if (!$limits instanceof ArrayObject) {
            throw new InvalidArgumentException("Limits is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        return $this->addEntry('Limits', $limits);
    } 
}