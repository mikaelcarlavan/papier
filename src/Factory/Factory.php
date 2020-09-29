<?php

namespace Papier\Factory;

use Papier\Validator\StringValidator;
use InvalidArgumentException;

class Factory
{
    /**
    * The number of the object.
    *
    * @var int
    */
    protected static $number = 1;
  
    /**
    * Instance of the object.
    *
    * @var Factory
    */
    protected static $instance = null;

    /**
     * Create a new instance of type
     *
     * @param string $type
     * @param mixed $value
     * @param bool $isIndirect
     * @return mixed
     * @throws InvalidArgumentException if the provided type's object does not exist.
     */
    public static function create(string $type, $value = null, $isIndirect = false)
    {
        $instance = self::getInstance();

        if (!StringValidator::isValid($type)) {
            throw new InvalidArgumentException("$type is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $class = 'Papier\Type\\'.ucfirst($type).'Type';
        
        if (!class_exists($class)) {
            throw new InvalidArgumentException("$class does not exist. See ".__CLASS__." class's documentation for possible values.");
        }

        $object = new $class();

        if ($isIndirect) {
            $object->setNumber($instance::$number);
            $object->setIndirect();
            $instance::$number++;
        }
               
        if (!is_null($value)) {
            $object->setValue($value);
        }

        
        return $object;
    }
   
    /**
    * Get instance of factory.
    *
    * @return Factory
    */
    public static function getInstance() 
    {
        if (is_null(self::$instance)) {
            self::$instance = new Factory();  
        }

        return self::$instance;
    }
}