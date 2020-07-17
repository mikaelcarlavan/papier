<?php

namespace Papier\Factory;

use Papier\Type\ArrayType;
use Papier\Type\ASCIIStringType;
use Papier\Type\BooleanType;
use Papier\Type\ByteStringType;
use Papier\Type\DateType;
use Papier\Type\DictionaryType;
use Papier\Type\FileSpecificationType;
use Papier\Type\FunctionType;
use Papier\Type\IntegerType;
use Papier\Type\NameTreeType;
use Papier\Type\NameType;
use Papier\Type\NullType;
use Papier\Type\NumberTreeType;
use Papier\Type\NumberType;
use Papier\Type\PDFDocEncodedStringType;
use Papier\Type\RectangleType;
use Papier\Type\StreamType;
use Papier\Type\StringType;
use Papier\Type\TextStreamType;
use Papier\Type\TextStringType;

use Papier\Validator\StringValidator;
use InvalidArgumentException;

class TypeFactory
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

        $class = ucfirst($type).'Type';

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Type does not exist. See ".get_class($this)." class's documentation for possible values.");
        }

        $object = new $class();
        return $object;
    }
}