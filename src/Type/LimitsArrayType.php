<?php

namespace Papier\Type;

class LimitsArrayType extends ArrayObject
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
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