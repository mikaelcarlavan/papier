<?php

namespace Papier\Font\TrueType;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Validator\ArrayValidator;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class TrueTypeFontIndexToLocationTable extends TrueTypeFontTable
{
	
	/**
	 * Location
	 *
	 * @var array
	 */
	protected array $location;

	/**
	 * Format
	 *
	 * @var int
	 */
	protected int $indexToLocFormat = 0;

	/**
	 * Number of glyphs.
	 *
	 * @var int
	 */
	protected int $numGlyphs = 0;

	/**
	 * Set the number of glyphs.
	 *
	 * @param int $numGlyphs
	 * @return TrueTypeFontIndexToLocationTable
	 */
	public function setNumGlyphs(int $numGlyphs): TrueTypeFontIndexToLocationTable
	{
		if (!IntegerValidator::isValid($numGlyphs)) {
			throw new InvalidArgumentException("Number glyphs is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->numGlyphs = $numGlyphs;
		return $this;
	}

	/**
	 * Get the number of glyphs.
	 *
	 * @return int
	 */
	public function getNumGlyphs(): int
	{
		return $this->numGlyphs;
	}

	/**
	 * Sets the number of horizontal metrics.
	 *
	 * @param int $indexToLocFormat The number of long horizontal metrics.
	 * @return TrueTypeFontIndexToLocationTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setIndexToLocFormat(int $indexToLocFormat): TrueTypeFontIndexToLocationTable
	{
		if (!IntegerValidator::isValid($indexToLocFormat)) {
			throw new InvalidArgumentException("Index to location format is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->indexToLocFormat = $indexToLocFormat;
		return $this;
	}

	/**
	 * Gets the number of horizontal metrics.
	 *
	 * @return int
	 */
	public function getIndexToLocFormat(): int
	{
		return $this->indexToLocFormat;
	}

	/**
	 * Extract table's data
	 */
	public function parse(): void
	{
		/** @var TrueTypeFontFileHelper $stream */
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);

		$format = $this->getIndexToLocFormat();

		$location = [];
		for ($i = 0; $i < $this->getNumGlyphs(); $i++) {
			$location[] = $format == 0 ? $stream->unpackOffset16() : $stream->unpackOffset32();
		}

		$this->setLocation($location);
	}

	/**
	 * Set location.
	 *
	 * @param array $location
	 * @return TrueTypeFontIndexToLocationTable
	 */
	public function setLocation(array $location): TrueTypeFontIndexToLocationTable
	{
		if (!ArrayValidator::isValid($location)) {
			throw new InvalidArgumentException("Location is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->location = $location;
		return $this;
	}

	/**
	 * Get location
	 *
	 * @return array
	 */
	public function getLocation(): array
	{
		return $this->location;
	}
}