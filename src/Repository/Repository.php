<?php

namespace Papier\Repository;

use Papier\Object\ArrayObject;
use Papier\Base\IndirectObject;

class Repository extends ArrayObject
{
    /**
    * Instance of the object.
    *
    * @var ?Repository
    */
    protected static ?Repository $instance = null;
   
    /**
    * Get instance of repository.
    *
    * @return Repository
    */
    public static function getInstance(): Repository
    {
        if (is_null(self::$instance)) {
            self::$instance = new Repository();  
        }

        return self::$instance;
    }

    /**
     * Add object to repository.
     *
     * @param IndirectObject $object
     * @return Repository
     */
    public function addObject(IndirectObject $object): Repository
    {
        $objects = $this->getObjects();
        $objects[$object->getNumber()] = $object;

        return $this->setObjects($objects);
    }

    /**
     * Remove object from repository.
     *
     * @param IndirectObject $object
     * @return Repository
     */
    public function removeObject(IndirectObject $object): Repository
    {
        $objects = $this->getObjects();
        unset($objects[$object->getNumber()]);

        return $this->setObjects($objects);
    }
}