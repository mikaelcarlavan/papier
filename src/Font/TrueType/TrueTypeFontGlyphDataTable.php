<?php

namespace Papier\Font\TrueType;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Validator\ArrayValidator;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class TrueTypeFontGlyphDataTable extends TrueTypeFontTable
{

	/**
	 * Data
	 *
	 * @var array
	 */
	protected array $data;

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
	 * Location
	 *
	 * @var array
	 */
	protected array $location;


	/**
	 * Set the number of glyphs.
	 *
	 * @param int $numGlyphs
	 * @return TrueTypeFontGlyphDataTable
	 */
	public function setNumGlyphs(int $numGlyphs): TrueTypeFontGlyphDataTable
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
	 * @return TrueTypeFontGlyphDataTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setIndexToLocFormat(int $indexToLocFormat): TrueTypeFontGlyphDataTable
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
		$multiplier = $format == 0 ? 1 : 2;

		$data = [];
		$location = $this->getLocation();

		for ($i = 0; $i < count($location); $i++) {
			$localOffset = $location[$i];
			$stream->setOffset($offset + $localOffset * $multiplier);

			$data[] = [
				'numberOfContours' => $stream->unpackShortInteger(),
				'xMin' => $stream->unpackShortInteger(),
				'yMin' => $stream->unpackShortInteger(),
				'xMax' => $stream->unpackShortInteger(),
				'yMax' => $stream->unpackShortInteger(),
			];
		}

		$this->setData($data);
	}

	/**
	 * Set data.
	 *
	 * @param array $data
	 * @return TrueTypeFontGlyphDataTable
	 */
	public function setData(array $data): TrueTypeFontGlyphDataTable
	{
		if (!ArrayValidator::isValid($data)) {
			throw new InvalidArgumentException("Data is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->data = $data;
		return $this;
	}

	/**
	 * Get data
	 *
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Set location.
	 *
	 * @param array $location
	 * @return TrueTypeFontGlyphDataTable
	 */
	public function setLocation(array $location): TrueTypeFontGlyphDataTable
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