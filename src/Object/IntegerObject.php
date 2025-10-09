<?php

namespace Papier\Object;

use InvalidArgumentException;
use Papier\Validator\IntegerValidator;

class IntegerObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param mixed $value
    * @return IntegerObject
    * @throws InvalidArgumentException if the provided argument is not of type 'int'.
    */
    public function setValue(mixed $value): IntegerObject
    {
        if (!IntegerValidator::isValid($value)) {
            throw new InvalidArgumentException("Integer is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        parent::setValue($value);
        return $this;
    }

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return IntegerObject
	 */
	public static function fromString(string $data): IntegerObject
	{
		$object = new IntegerObject();

		// Trim whitespace
		$data = trim($data);

		// Validate integer
		if (!ctype_digit($data) && !preg_match('/^-?\d+$/', $data)) {
			throw new InvalidArgumentException("Value is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$object->setValue((int)$data);

		return $object;
	}
}