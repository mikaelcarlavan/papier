<?php

namespace Papier\Font\TrueType;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Validator\ArrayValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RealValidator;
use InvalidArgumentException;

class TrueTypeFontKerningTable extends TrueTypeFontTable
{
	/**
	 * Version.
	 *
	 * @var int
	 */
	protected int $version;

	/**
	 * Number of sub tables.
	 *
	 * @var int
	 */
	protected int $nTables;


	/**
	 * Kerning pairs.
	 *
	 * @var array
	 */
	protected array $kerningPairs = [];

	/**
	 * Set table's version.
	 *
	 * @param int $version
	 * @return TrueTypeFontKerningTable
	 */
	public function setVersion(int $version = 0): TrueTypeFontKerningTable
	{
		if (!IntegerValidator::isValid($version)) {
			throw new InvalidArgumentException("Version is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->version = $version;

		return $this;
	}

	/**
	 * Get table's version
	 *
	 * @return int
	 */
	public function getVersion(): int
	{
		return $this->version;
	}

	/**
	 * Set kerning pairs.
	 *
	 * @param array $kerningPairs
	 * @return TrueTypeFontKerningTable
	 */
	public function setKerningPairs(array $kerningPairs): TrueTypeFontKerningTable
	{
		if (!ArrayValidator::isValid($kerningPairs)) {
			throw new InvalidArgumentException("Kerning pairs is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->kerningPairs = $kerningPairs;

		return $this;
	}

	/**
	 * Get kerning pairs.
	 *
	 * @return array
	 */
	public function getKerningPairs(): array
	{
		return $this->kerningPairs;
	}

	/**
	 * Set number of sub tables.
	 *
	 * @param int $nTables
	 * @return TrueTypeFontKerningTable
	 */
	public function setNumberOfSubTables(int $nTables = 0): TrueTypeFontKerningTable
	{
		if (!IntegerValidator::isValid($nTables)) {
			throw new InvalidArgumentException("Number of tables is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->nTables = $nTables;

		return $this;
	}

	/**
	 * Get number of sub tables.
	 *
	 * @return int
	 */
	public function getNumberOfSubTables(): int
	{
		return $this->nTables;
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

		$this->setVersion($stream->unpackUnsignedShortInteger());
		$this->setNumberOfSubTables($stream->unpackUnsignedShortInteger());

		$kerningPairs = [];
		for ($i = 0; $i < $this->getNumberOfSubTables(); $i++) {
			$version = $stream->unpackUnsignedShortInteger();
			$length = $stream->unpackUnsignedShortInteger();
			$coverage = $stream->unpackUnsignedShortInteger();

			$format = $coverage >> 8;
			if ($format != 0) {
				continue;
			}

			$nPairs = $stream->unpackUnsignedShortInteger();
			$searchRange = $stream->unpackUnsignedShortInteger();
			$entrySelector = $stream->unpackUnsignedShortInteger();
			$rangeShift = $stream->unpackUnsignedShortInteger();

			for ($j = 0; $j < $nPairs; $j++) {
				$left = $stream->unpackUnsignedShortInteger();
				$right = $stream->unpackUnsignedShortInteger();
				$value = $stream->unpackUnsignedShortInteger();

				$kerningPairs[$left][$right] = $value;
			}
		}

		$this->setKerningPairs($kerningPairs);
	}
}