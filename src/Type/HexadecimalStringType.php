<?php

namespace Papier\Type;

use Papier\Object\StringObject;

class HexadecimalStringType extends StringObject
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $chars = str_split($this->getValue());

        $value = '';
        if (is_array($chars)) {
            foreach ($chars as $char) {
                $value .= str_pad(dechex(ord($char)), 2, "0", STR_PAD_LEFT);
            }
        }

        return '<' .$value. '>';
    }
}