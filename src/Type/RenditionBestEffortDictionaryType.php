<?php

namespace Papier\Type;

use Papier\Type\Base\DictionaryType;

class RenditionBestEffortDictionaryType extends DictionaryType
{
	/**
	 * Set media criteria dictionary.
	 *
	 * @param  DictionaryType  $C
	 * @return RenditionBestEffortDictionaryType
	 */
	public function setC(DictionaryType $C): RenditionBestEffortDictionaryType
	{
		$this->setEntry('C', $C);
		return $this;
	}
}