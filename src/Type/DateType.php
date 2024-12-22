<?php

namespace Papier\Type;

use Cassandra\Date;
use Papier\Object\IntegerObject;

use Papier\Validator\DateValidator;

use DateTime;
use InvalidArgumentException;

class DateType extends IntegerObject
{
    /**
    * Set object's value.
    *
    * @param DateTime|string $value
    * @return DateType
    * @throws InvalidArgumentException if the provided argument is not of type 'DateTime' or convertible to.
    */
    public function setValue(mixed $value): DateType
    {
        if (!DateValidator::isValid($value)) {
            throw new InvalidArgumentException("Date is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		if (!$value instanceof DateTime) {
			$date = new DateTime();
			$timestamp = strtotime($value);
			/** @var int $timestamp */
			$date->setTimestamp($timestamp);
			parent::setValue($date);
		} else {
			parent::setValue($value);
		}


        return $this;
    } 

     /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue(): string
    {
		/** @var DateTime $date */
		$date = parent::getValue();

        $value = 'D:'.$date->format('YmdHis') . substr_replace($date->format('O'), "'", 3, 0);
        return '('.$value.')';
    }
}