<?php

namespace Papier\Type;

use Papier\Object\StringObject;


class LiteralStringType extends StringObject
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $value = $this->getValue();

        $trans = array('(' => '\(', ')' => '\)', '\\' => '\\\\');
        $value = strtr($value, $trans);

        return '(' .$value. ')';
    }
}