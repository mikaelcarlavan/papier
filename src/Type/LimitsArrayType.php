<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Type\LiteralStringType;
use Papier\Base\IndirectObject;

use InvalidArgumentException;

class LimitsArrayType extends ArrayObject
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $objects = $this->getObjects() ?? array();
        $value = '';
        if (is_array($objects)) {
            foreach ($objects as $object) {
                $value .= ' '.$object->format();
            }
        }

        return '[' .trim($value). ']';
    }
}