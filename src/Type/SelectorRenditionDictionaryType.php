<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Validator\ArrayValidator;
use InvalidArgumentException;

class SelectorRenditionDictionaryType extends RenditionDictionaryType
{
	/**
	 * Set rendition objects
	 *
	 * @param  array<mixed>  $r
	 * @return SelectorRenditionDictionaryType
	 */
	public function setR(array $r): SelectorRenditionDictionaryType
	{
		if (!ArrayValidator::isValid($r)) {
			throw new InvalidArgumentException("R is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $r);

		$this->setEntry('R', $value);
		return $this;
	}
}