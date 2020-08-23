<?php

namespace Papier\Type;

use Papier\Type\LiteralStringType;

class TextStringType extends LiteralStringType
{
    /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue()
    {
        $value = parent::getValue();
        return mb_convert_encoding($value, 'UTF-16BE');
    }
}