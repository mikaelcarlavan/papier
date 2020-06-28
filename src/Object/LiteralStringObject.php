<?php

namespace Papier\Object;

use Papier\Object\Base\StringObject;


class LiteralStringObject extends StringObject
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $value = $this->getValue();

        $trans = array('(' => '\(', ')' => '\)', '\\' => '\\\\');
        $value = strtr($value, $trans);

        return '('.$value.')';
    }
}