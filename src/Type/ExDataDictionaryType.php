<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;

class ExDataDictionaryType extends DictionaryType
{
	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'ExData');
		$this->setEntry('Type', $type);

		$type = Factory::create('Papier\Type\Base\NameType', 'Markup3D');
		$this->setEntry('Subtype', $type);

		return parent::format();
	}

}