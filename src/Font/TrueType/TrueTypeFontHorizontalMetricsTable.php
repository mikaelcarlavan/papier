<?php

namespace Papier\Font\TrueType;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Validator\ArrayValidator;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class TrueTypeFontHorizontalMetricsTable extends TrueTypeFontTable
{
	/**
	 * Horizontal metrics
	 *
	 * @var array
	 */
	protected array $hMetrics;

	/**
	 * Left side bearings
	 *
	 * @var array
	 */
	protected array $leftSideBearing;

	/**
	 * The number of horizontal metrics in the font.
	 *
	 * @var int
	 */
	protected int $numOfLongHorMetrics = 0;

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
	 * @return TrueTypeFontHorizontalMetricsTable
	 */
	public function setNumGlyphs(int $numGlyphs): TrueTypeFontHorizontalMetricsTable
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
	 * @param int $numOfLongHorMetrics The number of long horizontal metrics.
	 * @return TrueTypeFontHorizontalMetricsTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setNumOfLongHorMetrics(int $numOfLongHorMetrics): TrueTypeFontHorizontalMetricsTable
	{
		if (!IntegerValidator::isValid($numOfLongHorMetrics)) {
			throw new InvalidArgumentException("Num of Long Hor Metrics is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->numOfLongHorMetrics = $numOfLongHorMetrics;
		return $this;
	}

	/**
	 * Gets the number of horizontal metrics.
	 *
	 * @return int
	 */
	public function getNumOfLongHorMetrics(): int
	{
		return $this->numOfLongHorMetrics;
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

		$hMetrics = [];
		for ($i = 0; $i < $this->getNumOfLongHorMetrics(); $i++) {
			$hMetrics[] = [
				'advanceWidth' => $stream->unpackUnsignedShortInteger(),
				'leftSideBearing' => $stream->unpackUnsignedShortInteger()
			];
		}

		$leftSideBearing = [];
		for ($i = 0; $i < $this->getNumGlyphs() - $this->getNumOfLongHorMetrics(); $i++) {
			$leftSideBearing[] = $stream->unpackFWord();
		}

		$this->setHMetrics($hMetrics);
		$this->setLeftSideBaring($leftSideBearing);
	}

	/**
	 * Set horizontal metrics.
	 *
	 * @param array $hMetrics
	 * @return TrueTypeFontHorizontalMetricsTable
	 */
	public function setHMetrics(array $hMetrics): TrueTypeFontHorizontalMetricsTable
	{
		if (!ArrayValidator::isValid($hMetrics)) {
			throw new InvalidArgumentException("Horizontal metrics is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->hMetrics = $hMetrics;
		return $this;
	}

	/**
	 * Get horizontal metrics
	 *
	 * @return array
	 */
	public function getHMetrics(): array
	{
		return $this->hMetrics;
	}

	/**
	 * Set left side bearing
	 *
	 * @param array $leftSideBearing
	 * @return TrueTypeFontHorizontalMetricsTable
	 */
	public function setLeftSideBaring(array $leftSideBearing): TrueTypeFontHorizontalMetricsTable
	{
		if (!ArrayValidator::isValid($leftSideBearing)) {
			throw new InvalidArgumentException("Left side bearing is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->leftSideBearing = $leftSideBearing;
		return $this;
	}

	/**
	 * Get left side bearing
	 *
	 * @return array
	 */
	public function getLeftSideBaring(): array
	{
		return $this->leftSideBearing;
	}
}