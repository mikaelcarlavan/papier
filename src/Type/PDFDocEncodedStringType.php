<?php

namespace Papier\Type;

use Papier\Type\StringType;

class PDFDocEncodedStringType extends StringType
{
     /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue()
    {
        $value = parent::getValue();
        return mb_convert_encoding($value, 'ISO-8859-1');
    }
}