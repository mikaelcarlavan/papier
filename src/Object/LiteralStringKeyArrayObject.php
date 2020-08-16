<?php

namespace Papier\Object;

use Papier\Object\DictionaryObject;
use Papier\Object\LiteralStringObject;
use Papier\Base\IndirectObject;

use Papier\Factory\Factory;

use InvalidArgumentException;

class LiteralStringKeyArrayObject extends DictionaryObject
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
                $name = Factory::getInstance()->createObject('LiteralString', $key, false);
                $value .= $name->format() .' '. $object->write();
            }
        }

        return '[' .$value. ']';
    }
}