<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use RuntimeException;
use InvalidArgumentException;

class TextAnnotationDictionaryType extends AnnotationDictionaryType
{
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