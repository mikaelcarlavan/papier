<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\MediaClipValidator;
use Papier\Validator\RenditionTypeValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;

class MediaClipDictionaryType extends DictionaryType
{
	/**
	 * Set subtype of media clip.
	 *
	 * @param  string  $s
	 * @return MediaClipDictionaryType
	 */
	public function setS(string $s): MediaClipDictionaryType
	{
		if (!MediaClipValidator::isValid($s)) {
			throw new InvalidArgumentException("S is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\NameType', $s);
		$this->setEntry('S', $value);
		return $this;
	}

	/**
	 * Set name of the media clip.
	 *
	 * @param  string  $n
	 * @return MediaClipDictionaryType
	 */
	public function setN(string $n): MediaClipDictionaryType
	{
		if (!StringValidator::isValid($n)) {
			throw new InvalidArgumentException("N is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\TextStringType', $n);
		$this->setEntry('N', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'MediaClip');
		$this->setEntry('Type', $type);

		return parent::format();
	}
}