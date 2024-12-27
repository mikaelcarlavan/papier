<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\RenditionTypeValidator;

class RenditionMustHonourDictionaryType extends DictionaryType
{
	/**
	 * Set media criteria dictionary.
	 *
	 * @param  DictionaryType  $C
	 * @return RenditionMustHonourDictionaryType
	 */
	public function setC(DictionaryType $C): RenditionMustHonourDictionaryType
	{
		$this->setEntry('C', $C);
		return $this;
	}
}