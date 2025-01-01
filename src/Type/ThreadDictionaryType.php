<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Type\Base\DictionaryType;
use InvalidArgumentException;
use RuntimeException;

class ThreadDictionaryType extends DictionaryType
{
	/**
	 * Set first bead in the thread.
	 *
	 * @param DictionaryType $f
	 * @return ThreadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
	 */
	public function setF(DictionaryType $f): ThreadDictionaryType
	{
		$this->setEntry('F', $f);
		return $this;
	}

	/**
	 * Set thread information dictionary containing information about the
	 * thread, such as its title, author, and creation date.
	 *
	 * @param DocumentInformationDictionaryType $i
	 * @return ThreadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
	 */
	public function setI(DocumentInformationDictionaryType $i): ThreadDictionaryType
	{
		$this->setEntry('I', $i);
		return $this;
	}

	/**
	 * Format thread's content.
	 *
	 * @return string
	 */
	public function format(): string
	{
		if (!$this->hasEntry('F')) {
			throw new RuntimeException("F is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		$type = Factory::create('Papier\Type\Base\NameType', 'Thread');
		$this->setEntry('Type', $type);

		return parent::format();
	}

}