<?php

namespace Papier\Type;

use Papier\Text\Encoding;

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
        return Encoding::toUTF16BE($value);
    }
}