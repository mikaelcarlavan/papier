<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class MinimumScreenSizeDictionaryType extends DictionaryType
{
	/**
	 * Set two non-negative integers. The width and height (in pixels) of the monitor.
	 *
	 * @param int $v
	 * @return MinimumScreenSizeDictionaryType
	 */
	public function setV(int $v): MinimumScreenSizeDictionaryType
	{
		if (!IntegerValidator::isValid($v)) {
			throw new InvalidArgumentException("V is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\IntegerType', $v);

		$this->setEntry('V', $value);

		return $this;
	}

	/**
	 * Set which monitor should be tested against
	 *
	 * @param int $m
	 * @return MinimumScreenSizeDictionaryType
	 */
	public function setM(int $m): MinimumScreenSizeDictionaryType
	{
		if (!IntegerValidator::isValid($m)) {
			throw new InvalidArgumentException("M is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\IntegerType', $m);

		$this->setEntry('M', $value);

		return $this;
	}


	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'MinScreenSize');
		$this->setEntry('Type', $type);

		return parent::format();
	}
}