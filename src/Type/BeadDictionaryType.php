<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use InvalidArgumentException;
use RuntimeException;

class BeadDictionaryType extends DictionaryType
{
	/**
	 * Set thread to which this bead belongs.
	 *
	 * @param DictionaryType $t
	 * @return BeadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setT(DictionaryType $t): BeadDictionaryType
	{
		$this->setEntry('T', $t);
		return $this;
	}

	/**
	 * Set next bead in the thread
	 *
	 * @param DictionaryType $n
	 * @return BeadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setN(DictionaryType $n): BeadDictionaryType
	{
		$this->setEntry('N', $n);
		return $this;
	}

	/**
	 * Set previous bead in the thread
	 *
	 * @param DictionaryType $v
	 * @return BeadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setV(DictionaryType $v): BeadDictionaryType
	{
		$this->setEntry('V', $v);
		return $this;
	}

	/**
	 * Set page object representing the page on which this bead appears.
	 *
	 * @param DictionaryType $p
	 * @return BeadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setP(DictionaryType $p): BeadDictionaryType
	{
		$this->setEntry('P', $p);
		return $this;
	}

	/**
	 * Set rectangle specifying the location of this bead on the page.
	 *
	 * @param RectangleNumbersArrayType $r
	 * @return BeadDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setR(RectangleNumbersArrayType $r): BeadDictionaryType
	{
		$this->setEntry('R', $r);
		return $this;
	}

	/**
	 * Format bead's content.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Bead');
		$this->setEntry('Type', $type);

		return parent::format();
	}
}