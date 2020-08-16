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
    * @var \Papier\Factory\Factory
    */
    protected static $instance = null;
  
    /**
    * Create a new instance of object
    *
    * @param  string  $type
    * @param  mixed  $value
    * @param  bool  $isIndirect
    * @throws InvalidArgumentException if the provided type's object does not exist.
    * @return mixed
    */   
    public static function createObject($type, $value = null, $isIndirect = true)
    {
        if (!StringValidator::isValid($type)) {
            throw new InvalidArgumentException("$type is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $class = 'Papier\Object\\'.ucfirst($type).'Object';
        
        if (!class_exists($class)) {
            throw new InvalidArgumentException("$class does not exist. See ".__CLASS__." class's documentation for possible values.");
        }

        $object = new $class();
        
        if ($isIndirect) {
            $object->setNumber(self::$number);
            $object->setIndirect();
            self::$number++;
        }
                          
        if (!is_null($value)) {
            $object->setValue($value);
        }

        
        return $object;
    }
 
    /**
    * Create a new instance of type
    *
    * @param  string  $type
    * @param  mixed  $value
    * @param  bool  $isIndirect
    * @throws InvalidArgumentException if the provided type's object does not exist.
    * @return mixed
    */   
    public static function createType($type, $value = null, $isIndirect = true)
    {
        if (!StringValidator::isValid($type)) {
            throw new InvalidArgumentException("$type is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $class = 'Papier\Type\\'.ucfirst($type).'Type';
        
        if (!class_exists($class)) {
            throw new InvalidArgumentException("$class does not exist. See ".__CLASS__." class's documentation for possible values.");
        }

        $object = new $class();
        
        if ($isIndirect) {
            $object->setNumber(self::$number);
            $object->setIndirect();
            self::$number++;
        }
               
        if (!is_null($value)) {
            $object->setValue($value);
        }

        
        return $object;
    }
   
    /**
    * Get instance of factory.
    *
    * @return \Papier\Factory\Factory
    */
    public static function getInstance() 
    {
        if(is_null(self::$instance)) {
            self::$instance = new Factory();  
        }

        return self::$instance;
    }
}