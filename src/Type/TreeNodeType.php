<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\LiteralStringKeyArrayObject;
use Papier\Object\LiteralStringObject;
use Papier\Object\LimitsArrayObject;

use InvalidArgumentException;
use RunTimeException;

class TreeNodeType extends DictionaryObject
{ 
    /**
     * Get kids.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Object\ArrayObject
     */
    public function getKids()
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
     * @param  \Papier\Object\StringKeyArrayObject  $names
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Document\DocumentCatalog
     */
    public function setNames($names)
    {
        if (!$names instanceof StringKeyArrayObject) {
            throw new InvalidArgumentException("Names is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasKey('Kids')) {
            throw new RunTimeException("Kids is already present. See ".__CLASS__." class's documentation for possible values.");  
        }

        return $this->setObjectForKey('Names', $names);
    } 

    /**
     * Get kids.
     *  
     * @throws RunTimeException if node already contains 'Names' key.
     * @return \Papier\Object\LiteralStringKeyArrayObject
     */
    public function getNames()
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
    private function setLimits($limits)
    {
        if (!$limits instanceof LimitsArrayObject) {
            throw new InvalidArgumentException("Limits is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return $this->setObjectForKey('Limits', $limits);
    } 


    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        // Compute limits
        $limits = new LimitsArrayObject();
        $objects = array();

        if ($this->hasKey('Kids')) {
            //Intermediate nodes
            $kids = $this->getKids();

            foreach ($kids as $kid) {
                                
                while ($kid instanceof TreeNodeType && $kid->hasKey('Kids')) {
                    $kid = $kid->getKids();
                }
                
                if ($kid->hasKey('Names')) {
                    $names = $kid['Names'];

                    if (count($names) > 0) {
                        for ($i=0; $i < count($names); $i+2) {
                            $objects[] = $names[$i]->format();
                        }
                    }
                }
            }
        } else if ($this->hasKey('Names')) {
            $names = $this->getObjectForKey('Names');
            $objects += $names->getKeys();       
        }

        if (count($objects)) {
            sort($objects);

            $first = new LiteralStringObject();
            $last = new LiteralStringObject();
                        
            $first->setValue(array_shift($objects));
            $last->setValue(array_pop($objects));
            
            $limits->append($first);
            $limits->append($last);
    
            $this->setLimits($limits);
        }

        $value = $this->getValue();
        asort($value);
        
        $this->setValue($value);
        
        return parent::format();
    }
}