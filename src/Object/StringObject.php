<?php

namespace Papier\Object;

use InvalidArgumentException;
use Papier\Validator\StringValidator;

class StringObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param mixed $value
    * @return StringObject
    * @throws InvalidArgumentException if the provided argument is not of type 'string'.
    */
    public function setValue(mixed $value): StringObject
    {
        if (!StringValidator::isValid($value)) {
            throw new InvalidArgumentException("String is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		parent::setValue($value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
		/** @var string $value */
        $value = $this->getValue();

        $trans = array('(' => '\(', ')' => '\)', '\\' => '\\\\');
        $value = strtr($value, $trans);

        return $value. ')';
    }
}