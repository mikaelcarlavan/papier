<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class MinimumBitDepthDictionaryType extends DictionaryType
{
	/**
	 * Set the minimum screen depth (in bits) of the monitor
	 *
	 * @param int $v
	 * @return MinimumBitDepthDictionaryType
	 */
	public function setV(int $v): MinimumBitDepthDictionaryType
	{
		if (!IntegerValidator::isValid($v)) {
			throw new InvalidArgumentException("V is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\IntegerType', $v);

		$this->setEntry('V', $value);

		return $this;
	}

	/**
	 * Set specifies which monitor the value of V should be tested agains
	 *
	 * @param int $m
	 * @return MinimumBitDepthDictionaryType
	 */
	public function setM(int $m): MinimumBitDepthDictionaryType
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
		$type = Factory::create('Papier\Type\Base\NameType', 'MinBitDepth');
		$this->setEntry('Type', $type);

		return parent::format();
	}
}