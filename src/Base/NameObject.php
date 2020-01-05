<?php

namespace Papier\Base;

use Papier\Base\Object;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class NameObject extends Object
{
    /**
    * Set object's value.
    *
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    * @return \Papier\Base\NameObject
    */
    public function setValue($value)
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See NameObject class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 
    

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $value = $this->getValue();

        $matches = array();
        if (preg_match_all('/(\#\d{2})/', $value, $matches)) {
            foreach ($matches[0] as $match) {
                $value = str_replace($match, chr(hexdec(substr($match, 1, 2))), $value);
            }
        }

        return '/'.$value;
    }
}