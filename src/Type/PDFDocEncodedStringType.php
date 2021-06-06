<?php

namespace Papier\Type;

class PDFDocEncodedStringType extends StringType
{
     /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue(): string
    {
        $value = parent::getValue();
        return mb_convert_encoding($value, 'ISO-8859-1');
    }
}