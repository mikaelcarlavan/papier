<?php

namespace Papier\Type;

use Papier\Object\BaseObject;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;
use Papier\Factory\Factory;

use InvalidArgumentException;

class RectangleNumbersArrayType extends NumbersArrayType
{
    /**
    * Set object's lower left X coordinate.
    *
    * @param  mixed  $coordinate
    * @return RectangleNumbersArrayType
    *@throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
    */
    public function setLowerLeftX($coordinate): RectangleNumbersArrayType
	{
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$object = $this->getObjectForKey(0);
		$object->setValue($coordinate);

        return $this;
    } 
    
    
    /**
    * Set object's lower left Y coordinate.
    *
    * @param  mixed  $coordinate
    * @return RectangleNumbersArrayType
    *@throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
	 */
    public function setLowerLeftY($coordinate): RectangleNumbersArrayType
	{
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$object = $this->getObjectForKey(1);
		$object->setValue($coordinate);

        return $this;
    } 

    /**
    * Set object's upper right X coordinate.
    *
    * @param  mixed  $coordinate
    * @return RectangleNumbersArrayType
    *@throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
	 */
    public function setUpperRightX($coordinate): RectangleNumbersArrayType
	{
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$object = $this->getObjectForKey(2);
		$object->setValue($coordinate);

		return $this;
    } 

    /**
    * Set object's upper right Y coordinate.
    *
    * @param  mixed  $coordinate
    * @return RectangleNumbersArrayType
    *@throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
	 */
    public function setUpperRightY($coordinate): RectangleNumbersArrayType
	{
        if (!NumberValidator::isValid($coordinate)) {
            throw new InvalidArgumentException("Number is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		$object = $this->getObjectForKey(3);
		$object->setValue($coordinate);

        return $this;
    }

	/**
	 * Set object's numbers.
	 *
	 * @param mixed $value
	 * @return RectangleNumbersArrayType
	 */
	public function setValue(mixed $value): RectangleNumbersArrayType
	{
		if (!NumbersArrayValidator::isValid($value)) {
			throw new InvalidArgumentException("Array is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		parent::setValue($value);
		return $this;
	}
}