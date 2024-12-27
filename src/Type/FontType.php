<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;

class FontType extends DictionaryType
{
	/**
	 * Get name.
	 *
	 * @return string|null
	 */
	public function getName(): ?string
	{
		/** @var string|null $value */
		$value = $this->getEntryValue('Name');
		return $value;
	}

	/**
	 * Set name.
	 *
	 * @param string $name
	 * @return FontType
	 */
	public function setName(string $name): FontType
	{
		$value = Factory::create('Papier\Type\Base\NameType', $name);
		$this->setEntry('Name', $value);
		return $this;
	}
}