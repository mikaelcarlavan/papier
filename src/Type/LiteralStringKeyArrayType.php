<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

class LiteralStringKeyArrayType extends DictionaryObject
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
			/** @var LiteralStringType $object */
			/** @var LiteralStringType $name */
			$name = Factory::create('Papier\Type\LiteralStringType', $key);
			$value .= $name->format() .' '. $object->write();
		}

        return '[' .$value. ']';
    }
}