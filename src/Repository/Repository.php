<?php

namespace Papier\Repository;

use Papier\Object\ArrayObject;
use Papier\Base\IndirectObject;

use InvalidArgumentException;

class Repository extends ArrayObject
{
    /**
    * Instance of the object.
    *
    * @var \Papier\Repository
    */
    protected static $instance = null;
   
    /**
    * Get instance of repository.
    *
    * @return \Papier\Repository
    */
    public static function getInstance() 
    {
        if(is_null(self::$instance)) {
            self::$instance = new Repository();  
        }

        return self::$instance;
    }

    /**
     * Add object to repository.
     *  
     * @param  \Papier\Base\IndirectObject  $object
     * @return \Papier\Repository
     */
    public function addObject($object)
    {        
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $objects = $this->getObjects();
        $objects[$object->getNumber()] = $object;

        return $this->setObjects($objects);
    }

    /**
     * Remove object from repository.
     *  
     * @param  \Papier\Base\IndirectObject  $object
     * @return \Papier\Repository
     */
    public function removeObject($object)
    {
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();
        unset($objects[$object->getNumber()]);

        return $this->setObjects($objects);
    }
}