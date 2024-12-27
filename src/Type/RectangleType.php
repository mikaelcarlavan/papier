<?php

namespace Papier\Type;

use Papier\Object\BaseObject;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;
use Papier\Factory\Factory;

use InvalidArgumentException;

class RectangleType extends NumbersArrayType
{
    /**
    * Set object's lower left X coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setLowerLeftX($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$values = $this->getValues();
		$values[0] = $coordinate;
		$this->setValue($values);

        return $this;
    } 
    
    
    /**
    * Set object's lower left Y coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setLowerLeftY($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$values = $this->getValues();
		$values[1] = $coordinate;
		$this->setValue($values);

        return $this;
    } 

    /**
    * Set object's upper right X coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setUpperRightX($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$values = $this->getValues();
		$values[2] = $coordinate;
		$this->setValue($values);
		return $this;
    } 

    /**
    * Set object's upper right Y coordinate.
    *
    * @param  mixed  $coordinate
    * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    * @return RectangleType
    */
    public function setUpperRightY($coordinate): RectangleType
    {
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$values = $this->getValues();
		$values[3] = $coordinate;
		$this->setValue($values);

        return $this;
    }

	/**
	 * Get object's numbers
	 *
	 * @return array<mixed>
	 */
	public function getValues(): array
	{
		$values = [];
		$objects = $this->getObjects();
		if (count($objects)) {
			foreach ($objects as $object) {
				$values[] = $object->getValue();
			}
		}
		return $values;
	}
	/**
	 * Set object's numbers.
	 *
	 * @param mixed $value
	 * @return RectangleType
	 */
	public function setValue(mixed $value): RectangleType
	{
		if (!NumbersArrayValidator::isValid($value)) {
			throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		parent::setValue($value);
		return $this;
	}
}