<?php

namespace Papier\Type;

class PDFDocEncodedStringType extends StringType
{
     /**
     * Get object's value.
     *
     * @return mixed
     */
    protected function getValue(): mixed
    {
        $value = parent::getValue();
        return mb_convert_encoding($value, 'ISO-8859-1');
    }
}