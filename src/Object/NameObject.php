<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;

use InvalidArgumentException;

class NameObject extends IndirectObject
{
    /**
     * Set object's value.
     *
     * @param mixed $value
     * @return NameObject
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setValue($value): NameObject
    {
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
        $value = $this->getValue();

        $trans = array(
            ' ' => '#20', '(' => '#28', ')' => '#29', '#' => '#23', '<' => '#3C', '>' => '#3E', 
            '[' => '#5B', ']' => '#5D', '{' => '#7B', '}' => '#7D', '/' => '#2F', '%' => '#25'
        );

        $value = strtr($value, $trans);

        return '/' .$value;
    }
}