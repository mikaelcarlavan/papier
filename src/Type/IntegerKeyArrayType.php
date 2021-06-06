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
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $name = Factory::create('Integer', $key);
                $value .= $name->format() .' '. $object->write();
            }
        }

        return '[' .$value. ']';
    }
}