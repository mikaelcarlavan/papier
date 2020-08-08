<?php

namespace Papier\Type;

use Papier\Type\StringType;
use DateTime;

class DateType extends StringType
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
        if (!$date instanceof DateTime) {
            throw new InvalidArgumentException("Date is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 

     /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue()
    {
        $value = parent::getValue();
        $value = 'D:'.$value->format('YmdHis') . substr_replace($now->format('O'), "'", 3, 0);
        return $value;
    }
}