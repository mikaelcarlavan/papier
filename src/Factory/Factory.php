<?php

namespace Papier\Factory;

use InvalidArgumentException;
use Papier\Type\ArrayType;
use Papier\Type\ASCIIStringType;
use Papier\Type\DictionaryType;
use Papier\Type\NameType;

class Factory
{
    /**
    * The number of the object.
    *
    * @var int
    */
    protected static int $number = 1;
  
    /**
    * Instance of the object.
    *
    * @var Factory|null
    */
    protected static ?Factory $instance = null;

    /**
     * Create a new instance of type
     *
     * @template T
     * @param class-string<T> $class
     * @param mixed|null $value
     * @param bool $isIndirect
     * @return T
     * @throws InvalidArgumentException if the provided type's object does not exist.
     */
    public static function create(string $class, mixed $value = null, bool $isIndirect = false)
    {
        $instance = self::getInstance();

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
    private static function getInstance(): Factory
    {
        if (is_null(self::$instance)) {
            self::$instance = new Factory();  
        }

        return self::$instance;
    }
}