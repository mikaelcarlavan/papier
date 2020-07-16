<?php

namespace Papier\Type;

use Papier\Type\StringType;

class TextStringType extends StringType
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