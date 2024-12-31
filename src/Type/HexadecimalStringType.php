<?php

namespace Papier\Type;

use Papier\Object\StringObject;
use Papier\Type\Base\StringType;

class HexadecimalStringType extends StringType
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
		/** @var string $value */
		$value = $this->getValue();
        $chars = str_split($value);

        $value = '';
		foreach ($chars as $char) {
			$value .= str_pad(dechex(ord($char)), 2, "0", STR_PAD_LEFT);
		}

        return '<' .$value. '>';
    }
}