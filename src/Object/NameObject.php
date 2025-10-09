<?php

namespace Papier\Object;

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
    public function setValue(mixed $value): NameObject
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
		/** @var string $value */
		$value = $this->getValue();

        $trans = array(
            ' ' => '#20', '(' => '#28', ')' => '#29', '#' => '#23', '<' => '#3C', '>' => '#3E', 
            '[' => '#5B', ']' => '#5D', '{' => '#7B', '}' => '#7D', '/' => '#2F', '%' => '#25'
        );

        $value = strtr($value, $trans);

        return '/' .$value;
    }

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return NameObject
	 */
	public static function fromString(string $data): NameObject
	{
		$object = new NameObject();

		// Trim whitespace and leading '/'
		$data = trim($data);
		$data = ltrim($data, '/');

		// Decode PDF-encoded hex sequences in the name (e.g., #20 → space)
		$data = preg_replace_callback('/#([0-9A-Fa-f]{2})/', function ($matches) {
			return chr(hexdec($matches[1]));
		}, $data);

		$object->setValue($data);

		return $object;
	}
}