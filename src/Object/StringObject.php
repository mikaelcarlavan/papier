<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

class StringObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    * @return StringObject
    */
    public function setValue($value): StringObject
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $value = $this->getValue();

        $trans = array('(' => '\(', ')' => '\)', '\\' => '\\\\');
        $value = strtr($value, $trans);

        return $value. ')';
    }
}