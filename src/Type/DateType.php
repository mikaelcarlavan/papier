<?php

namespace Papier\Type;

use Papier\Object\IntegerObject;

use Papier\Validator\DateValidator;

use DateTime;

class DateType extends IntegerObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $date
    * @throws InvalidArgumentException if the provided argument is not of type 'DateTime'.
    * @return \Papier\Type\DateType
    */
    public function setValue($date)
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("Date is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $timestamp = $date instanceof DateTime ? $date->format('U') : strtotime($date);

        return parent::setValue(intval($timestamp));
    } 

     /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue()
    {
        $value = parent::getValue();

        $date = new DateTime();
        $date->setTimestamp($value);

        $value = 'D:'.$date->format('YmdHis') . substr_replace($date->format('O'), "'", 3, 0);
        return '('.$value.')';
    }
}