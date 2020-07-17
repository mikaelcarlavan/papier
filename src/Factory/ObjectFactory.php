<?php

namespace Papier\Factory;

use Papier\Object\StringObject;
use Papier\Object\ArrayObject;
use Papier\Object\BooleanObject;
use Papier\Object\DictionaryObject;
use Papier\Object\HexadecimalStringObject;
use Papier\Object\IntegerObject;
use Papier\Object\LiteralStringObject;
use Papier\Object\NameObject;
use Papier\Object\NullObject;
use Papier\Object\RealObject;
use Papier\Object\StreamObject;

use Papier\Validator\StringValidator;
use InvalidArgumentException;

class ObjectFactory
{
    /**
    * Create a new instance of object
    *
    * @param  string  $type
    * @throws InvalidArgumentException if the provided type's object does not exist.
    * @return mixed
    */   
    public static function create($type)
    {
        if (!StringValidator::isValid($type)) {
            throw new InvalidArgumentException("Type is incorrect. See ".get_class($this)." class's documentation for possible values.");
        }

        $class = ucfirst($type).'Object';

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Type does not exist. See ".get_class($this)." class's documentation for possible values.");
        }

        $object = new $class();
        return $object;
    }
}