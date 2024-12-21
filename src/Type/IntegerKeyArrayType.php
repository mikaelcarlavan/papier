<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;


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
			$name = Factory::create('Papier\Type\IntegerType', $key);
			$value .= $name->format() .' '. $object->write();
		}

        return '[' .$value. ']';
    }
}