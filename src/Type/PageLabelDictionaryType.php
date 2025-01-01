<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\NameType;
use Papier\Validator\IntegerValidator;
use Papier\Validator\NumberingStyleValidator;
use InvalidArgumentException;
use Papier\Validator\StringValidator;

class PageLabelDictionaryType extends DictionaryType
{
	/**
	 * Set name.
	 *
	 * @param string $s
	 * @return PageLabelDictionaryType
	 */
	public function setS(string $s): PageLabelDictionaryType
	{
		if (!NumberingStyleValidator::isValid($s)) {
			throw new InvalidArgumentException("S is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\NameType', $s);
		$this->setEntry('S', $value);
		return $this;
	}

	/**
	 * Set label prefix for page labels in this range.
	 *
	 * @param string $p
	 * @return PageLabelDictionaryType
	 */
	public function setP(string $p): PageLabelDictionaryType
	{
		if (!StringValidator::isValid($p)) {
			throw new InvalidArgumentException("P is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\TextStringType', $p);
		$this->setEntry('P', $value);
		return $this;
	}

	/**
	 * Set label prefix for page labels in this range.
	 *
	 * @param int $st
	 * @return PageLabelDictionaryType
	 */
	public function setSt(int $st): PageLabelDictionaryType
	{
		if (!IntegerValidator::isValid($st, 1)) {
			throw new InvalidArgumentException("St is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\IntegerType', $st);
		$this->setEntry('St', $value);
		return $this;
	}
}