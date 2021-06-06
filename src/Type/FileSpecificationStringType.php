<?php

namespace Papier\Type;

use Papier\Object\StringObject;


class FileSpecificationStringType extends StringObject
{
    /**
     * Convert file specification.
     *
     * @return string
     */
    public function getConvertedValue(): string
    {
        $value = $this->getValue();

        // Check if network resource is present
        if (strpos($value, ':\\') !== false) { 
            $value = '/'.$value;
            $value = str_replace(':\\', '/', $value);
        }

        // Check for DOS absolute path
        if (substr($value, 0, 1) == '\\') {
            $value = '//'.substr($value, 1);
        }

        $trans = array('(' => '\(', ')' => '\)', '\\' => '/');
        return strtr($value, $trans);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $value = $this->getConvertedValue();
        return '(' .$value. ')';
    }
}