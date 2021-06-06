<?php

namespace Papier\Type;

use Papier\Object\IntegerObject;

use Papier\Validator\DateValidator;

use DateTime;
use InvalidArgumentException;

class DateType extends IntegerObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @return DateType
    * @throws InvalidArgumentException if the provided argument is not of type 'DateTime' or convertible to.
    */
    public function setValue($value): DateType
    {
        if (!DateValidator::isValid($value)) {
            throw new InvalidArgumentException("Date is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $timestamp = $value instanceof DateTime ? $value->format('U') : strtotime($value);

        return parent::setValue(intval($timestamp));
    } 

     /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue(): string
    {
        $value = parent::getValue();

        $date = new DateTime();
        $date->setTimestamp($value);

        $value = 'D:'.$date->format('YmdHis') . substr_replace($date->format('O'), "'", 3, 0);
        return '('.$value.')';
    }
}