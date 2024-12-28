<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class OutlineDictionaryType extends DictionaryType
{
	/**
	 * Set first.
	 *
	 * @param DictionaryType $first
	 * @return OutlineDictionaryType
	 */
	public function setFirst(DictionaryType $first): OutlineDictionaryType
	{
		$this->setEntry('First', $first);
		return $this;
	}

	/**
	 * Set last.
	 *
	 * @param DictionaryType $last
	 * @return OutlineDictionaryType
	 */
	public function setLast(DictionaryType $last): OutlineDictionaryType
	{
		$this->setEntry('Last', $last);
		return $this;
	}

	/**
	 * Set count.
	 *
	 * @param int $count
	 * @return OutlineDictionaryType
	 */
	public function setCount(int $count): OutlineDictionaryType
	{
		if (!IntegerValidator::isValid($count, 0)) {
			throw new InvalidArgumentException("Count is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\IntegerType', $count);

		$this->setEntry('Count', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Outlines');
		$this->setEntry('Type', $type);

		return parent::format();
	}
}