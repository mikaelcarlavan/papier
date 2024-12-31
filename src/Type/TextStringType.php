<?php

namespace Papier\Type;

use Papier\Text\Encoding;
use Papier\Type\Base\StringType;

class TextStringType extends StringType
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

		$trans = array('(' => '\(', ')' => '\)', '\\' => '\\\\');
		$value = strtr($value, $trans);
		$value = Encoding::toUTF16BE($value);
		return '(' .$value. ')';
    }
}