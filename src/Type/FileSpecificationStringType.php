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
    public function getConvertedValue()
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
        $value = strtr($value, $trans);

        return $value;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $value = $this->getConvertedValue();
        return '(' .$value. ')';
    }
}