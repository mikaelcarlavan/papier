<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DateType;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\AnnotationTypeValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BorderStyleValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;
use RuntimeException;

class BorderDictionaryType extends DictionaryType
{
	/**
	 * Set width.
	 *
	 * @param  mixed  $w
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
	 * @return BorderDictionaryType
	 */
	public function setW(mixed $w): BorderDictionaryType
	{
		if (!NumberValidator::isValid($w)) {
			throw new InvalidArgumentException("W is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\NumberType', $w);

		$this->setEntry('W', $value);
		return $this;
	}


	/**
	 * Set style
	 *
	 * @param  string  $s
	 * @return BorderDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setS(string $s): BorderDictionaryType
	{
		if (!BorderStyleValidator::isValid($s)) {
			throw new InvalidArgumentException("S is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $s);

		$this->setEntry('S', $value);
		return $this;
	}


	/**
	 * Set pattern of dashes and gaps that shall be used in drawing a dashed border
	 *
	 * @param  array  $d
	 * @return BorderDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'array'.
	 */
	public function setD(array $d): BorderDictionaryType
	{
		if (!ArrayValidator::isValid($d)) {
			throw new InvalidArgumentException("D is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\ArrayType', $d);

		$this->setEntry('D', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Border');
		$this->setEntry('Type', $type);

		if (!$this->hasEntry('Subtype')) {
			throw new RuntimeException("Subtype is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		return parent::format();
	}
}