<?php

namespace Papier\Type;

use Papier\Document\BorderEffect;
use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BorderEffectValidator;
use Papier\Validator\BorderStyleValidator;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;
use RuntimeException;

class BorderEffectDictionaryType extends DictionaryType
{

	/**
	 * Set border effect
	 *
	 * @param  string  $s
	 * @return BorderEffectDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setS(string $s): BorderEffectDictionaryType
	{
		if (!BorderEffectValidator::isValid($s)) {
			throw new InvalidArgumentException("S is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $s);

		$this->setEntry('S', $value);
		return $this;
	}

	/**
	 * Set intensity of the effect.
	 *
	 * @param  mixed  $i
	 * @return BorderEffectDictionaryType
	 *@throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
	 */
	public function setI(mixed $i): BorderEffectDictionaryType
	{
		if (!NumberValidator::isValid($i, 0, 2)) {
			throw new InvalidArgumentException("I is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\NumberType', $i);

		$this->setEntry('I', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		if ($this->hasEntry('S') && $this->getEntryValue('S') == BorderEffect::NO_EFFECT && $this->hasEntry('I') ) {
			throw new RuntimeException("I is not compatible with S. See ".__CLASS__." class's documentation for possible values.");
		}

		return parent::format();
	}
}