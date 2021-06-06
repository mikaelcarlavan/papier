<?php

namespace Papier\Type;

class TextStringType extends LiteralStringType
{
    /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue(): string
    {
        $value = parent::getValue();
        return mb_convert_encoding($value, 'UTF-16BE');
    }
}