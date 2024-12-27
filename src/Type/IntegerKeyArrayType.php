<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\IntegerType;


class IntegerKeyArrayType extends DictionaryObject
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $objects = $this->getObjects();
        
        $value = '';
		foreach ($objects as $key => $object) {
			/** @var IntegerType $object */
			$name = Factory::create('Papier\Type\Base\IntegerType', $key);
			$value .= $name->format() .' '. $object->write();
		}

        return '[' .$value. ']';
    }
}