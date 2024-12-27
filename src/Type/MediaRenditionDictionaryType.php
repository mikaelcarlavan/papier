<?php

namespace Papier\Type;

use Papier\Type\Base\DictionaryType;

class MediaRenditionDictionaryType extends RenditionDictionaryType
{
	/**
	 * Set media clip dictionary.
	 *
	 * @param  DictionaryType  $c
	 * @return MediaRenditionDictionaryType
	 */
	public function setC(DictionaryType $c): MediaRenditionDictionaryType
	{
		$this->setEntry('C', $c);
		return $this;
	}

	/**
	 * Set media play parameters dictionary.
	 *
	 * @param  DictionaryType  $p
	 * @return MediaRenditionDictionaryType
	 */
	public function setP(DictionaryType $p): MediaRenditionDictionaryType
	{
		$this->setEntry('P', $p);
		return $this;
	}

	/**
	 * Set media screen parameters dictionary.
	 *
	 * @param  DictionaryType  $sp
	 * @return MediaRenditionDictionaryType
	 */
	public function setSP(DictionaryType $sp): MediaRenditionDictionaryType
	{
		$this->setEntry('SP', $sp);
		return $this;
	}
}