<?php

namespace Papier\Type;

use Papier\Object\BaseObject;
use Papier\Type\Base\ArrayType;

class LimitsArrayType extends ArrayType
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
		foreach ($objects as $object) {
			/** @var BaseObject $object */
			$value .= ' '.$object->format();
		}

        return '[' .trim($value). ']';
    }
}