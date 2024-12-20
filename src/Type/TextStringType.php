<?php

namespace Papier\Type;

use Papier\Text\Encoding;

class TextStringType extends LiteralStringType
{
    /**
     * Get object's value.
     *
     * @return ?string
     */
    protected function getValue(): ?string
    {
		/** @var string|null $value */
        $value = parent::getValue();
        return is_null($value) ? $value : Encoding::toUTF16BE($value);
    }
}