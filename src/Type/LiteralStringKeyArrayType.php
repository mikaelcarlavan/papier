<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Base\IndirectObject;

use Papier\Factory\Factory;

use InvalidArgumentException;

class LiteralStringKeyArrayType extends DictionaryObject
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getObjects();
        
        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $key => $object) {
                $name = Factory::create('LiteralString', $key);
                $value .= $name->format() .' '. $object->write();
            }
        }

        return '[' .$value. ']';
    }
}