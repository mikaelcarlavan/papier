<?php

namespace Papier\Type;

use Papier\Object\ArrayObject;
use Papier\Object\StringObject;
use Papier\Type\Base\StringType;

class HexadecimalStringType extends StringType
{

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
		/** @var string $value */
		$value = $this->getValue();
        $chars = str_split($value);

        $value = '';
		foreach ($chars as $char) {
			$value .= str_pad(dechex(ord($char)), 2, "0", STR_PAD_LEFT);
		}

        return '<' .$value. '>';
    }

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return HexadecimalStringType
	 */
	public static function fromString(string $data): HexadecimalStringType
	{
		$object = new HexadecimalStringType();

		// Clean input: remove < and >
		$data = trim($data);
		$data = trim($data, '<>');

		// Normalize possible odd-length hex
		if (strlen($data) % 2 !== 0) {
			$data .= '0';
		}

		// Convert hex to raw string
		$decoded = '';
		for ($i = 0; $i < strlen($data); $i += 2) {
			$decoded .= chr(hexdec(substr($data, $i, 2)));
		}

		$object->setValue($decoded);

		return $object;
	}
}