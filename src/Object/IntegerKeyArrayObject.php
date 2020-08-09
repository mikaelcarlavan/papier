<?php

namespace Papier\Object;

use Papier\Object\DictionaryObject;
use Papier\Object\IntegerObject;
use Papier\Base\IndirectObject;

use InvalidArgumentException;

class IntegerKeyArrayObject extends DictionaryObject
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
                $name = new IntegerObject();
                $name->setValue($key);

                $value .= $name->format() .' '. $object->write();
            }
        }

        return '[' .$value. ']';
    }
}