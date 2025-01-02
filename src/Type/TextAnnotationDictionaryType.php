<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Validator\BooleanValidator;
use RuntimeException;
use InvalidArgumentException;

class TextAnnotationDictionaryType extends MarkupAnnotationDictionaryType
{
	/**
	 * Set whether the annotation shall initially be displayed open
	 *
	 * @param bool $open
	 * @return TextAnnotationDictionaryType
	 */
	public function setOpen(bool $open): TextAnnotationDictionaryType
	{
		if (!BooleanValidator::isValid($open)) {
			throw new InvalidArgumentException("Open is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\BooleanType', $open);
		$this->setEntry('Open', $value);
		return $this;
	}
	
	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Text');
		$this->setEntry('Subtype', $type);

		return parent::format();
	}
}