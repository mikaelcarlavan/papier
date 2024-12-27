<?php

namespace Papier\Font;

use Papier\Validator\RealValidator;
use Papier\Validator\IntegerValidator;

use InvalidArgumentException;

class TrueTypeFontHorizontalHeaderTable extends TrueTypeFontTable
{
	/**
	 * The version of the HHEA table.
	 *
	 * @var float
	 */
	protected float $version;

	/**
	 * The ascent value in font units.
	 *
	 * @var int
	 */
	protected int $ascent;

	/**
	 * The descent value in font units.
	 *
	 * @var int
	 */
	protected int $descent;

	/**
	 * The line gap in font units.
	 *
	 * @var int
	 */
	protected int $lineGap;

	/**
	 * The maximum advance width in font units.
	 *
	 * @var int
	 */
	protected int $advanceWidthMax;

	/**
	 * The minimum left side bearing in font units.
	 *
	 * @var int
	 */
	protected int $minLeftSideBearing;

	/**
	 * The minimum right side bearing in font units.
	 *
	 * @var int
	 */
	protected int $minRightSideBearing;

	/**
	 * The maximum extent along the x-axis in font units.
	 *
	 * @var int
	 */
	protected int $xMaxExtent;

	/**
	 * The slope of the caret in the rise direction.
	 *
	 * @var int
	 */
	protected int $caretSlopeRise;

	/**
	 * The slope of the caret in the run direction.
	 *
	 * @var int
	 */
	protected int $caretSlopeRun;

	/**
	 * The caret offset in font units.
	 *
	 * @var int
	 */
	protected int $caretOffset;

	/**
	 * Reserved field for future use.
	 *
	 * @var int
	 */
	protected int $reserved1;

	/**
	 * Reserved field for future use.
	 *
	 * @var int
	 */
	protected int $reserved2;

	/**
	 * Reserved field for future use.
	 *
	 * @var int
	 */
	protected int $reserved3;

	/**
	 * Reserved field for future use.
	 *
	 * @var int
	 */
	protected int $reserved4;

	/**
	 * The format of the metric data (expected to be 0).
	 *
	 * @var int
	 */
	protected int $metricDataFormat;

	/**
	 * The number of horizontal metrics in the font.
	 *
	 * @var int
	 */
	protected int $numOfLongHorMetrics;

	/**
	 * Extract table's data
	 */
	public function parse(): void
	{
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);

		$this->setVersion($stream->unpackFixed());
		$this->setAscent($stream->unpackFWord());
		$this->setDescent($stream->unpackFWord());
		$this->setLineGap($stream->unpackFWord());
		$this->setAdvanceWidthMax($stream->unpackUnsignedFWord());
		$this->setMinLeftSideBearing($stream->unpackFWord());
		$this->setMinRightSideBearing($stream->unpackFWord());
		$this->setXMaxExtent($stream->unpackFWord());
		$this->setCaretSlopeRise($stream->unpackShortInteger());
		$this->setCaretSlopeRun($stream->unpackShortInteger());
		$this->setCaretOffset($stream->unpackFWord());
		$this->setReserved1($stream->unpackShortInteger());
		$this->setReserved2($stream->unpackShortInteger());
		$this->setReserved3($stream->unpackShortInteger());
		$this->setReserved4($stream->unpackShortInteger());
		$this->setMetricDataFormat($stream->unpackShortInteger());
		$this->setNumOfLongHorMetrics($stream->unpackUnsignedShortInteger());
	}

	/**
	 * Set table's version.
	 *
	 * @param float $version
	 * @return TrueTypeFontHorizontalHeaderTable
	 */
	public function setVersion(float $version = 0): TrueTypeFontHorizontalHeaderTable
	{
		if (!RealValidator::isValid($version)) {
			throw new InvalidArgumentException("Version is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->version = $version;

		return $this;
	}

	/**
	 * Get table's version
	 *
	 * @return float
	 */
	public function getVersion(): float
	{
		return $this->version;
	}

	/**
	 * Sets the ascent value in font units.
	 *
	 * @param int $ascent The ascent value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setAscent(int $ascent): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($ascent)) {
			throw new InvalidArgumentException("Ascent is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->ascent = $ascent;
		return $this;
	}

	/**
	 * Gets the ascent value in font units.
	 *
	 * @return int
	 */
	public function getAscent(): int
	{
		return $this->ascent;
	}

	/**
	 * Sets the descent value in font units.
	 *
	 * @param int $descent The descent value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setDescent(int $descent): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($descent)) {
			throw new InvalidArgumentException("Descent is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->descent = $descent;
		return $this;
	}

	/**
	 * Gets the descent value in font units.
	 *
	 * @return int
	 */
	public function getDescent(): int
	{
		return $this->descent;
	}

	/**
	 * Sets the line gap in font units.
	 *
	 * @param int $lineGap The line gap value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setLineGap(int $lineGap): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($lineGap)) {
			throw new InvalidArgumentException("Line Gap is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->lineGap = $lineGap;
		return $this;
	}

	/**
	 * Gets the line gap in font units.
	 *
	 * @return int
	 */
	public function getLineGap(): int
	{
		return $this->lineGap;
	}

	/**
	 * Sets the maximum advance width in font units.
	 *
	 * @param int $advanceWidthMax The maximum advance width.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the advance width value is not valid.
	 */
	public function setAdvanceWidthMax(int $advanceWidthMax): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($advanceWidthMax)) {
			throw new InvalidArgumentException("Advance Width Max is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->advanceWidthMax = $advanceWidthMax;
		return $this;
	}

	/**
	 * Gets the maximum advance width in font units.
	 *
	 * @return int
	 */
	public function getAdvanceWidthMax(): int
	{
		return $this->advanceWidthMax;
	}

	/**
	 * Sets the minimum left side bearing in font units.
	 *
	 * @param int $minLeftSideBearing The minimum left side bearing.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the left side bearing value is not valid.
	 */
	public function setMinLeftSideBearing(int $minLeftSideBearing): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($minLeftSideBearing)) {
			throw new InvalidArgumentException("Min Left Side Bearing is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->minLeftSideBearing = $minLeftSideBearing;
		return $this;
	}

	/**
	 * Gets the minimum left side bearing in font units.
	 *
	 * @return int
	 */
	public function getMinLeftSideBearing(): int
	{
		return $this->minLeftSideBearing;
	}

	/**
	 * Sets the minimum right side bearing in font units.
	 *
	 * @param int $minRightSideBearing The minimum right side bearing.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the right side bearing value is not valid.
	 */
	public function setMinRightSideBearing(int $minRightSideBearing): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($minRightSideBearing)) {
			throw new InvalidArgumentException("Min Right Side Bearing is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->minRightSideBearing = $minRightSideBearing;
		return $this;
	}

	/**
	 * Gets the minimum right side bearing in font units.
	 *
	 * @return int
	 */
	public function getMinRightSideBearing(): int
	{
		return $this->minRightSideBearing;
	}

	/**
	 * Sets the maximum extent along the x-axis in font units.
	 *
	 * @param int $xMaxExtent The maximum extent.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the extent value is not valid.
	 */
	public function setXMaxExtent(int $xMaxExtent): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($xMaxExtent)) {
			throw new InvalidArgumentException("X Max Extent is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->xMaxExtent = $xMaxExtent;
		return $this;
	}

	/**
	 * Gets the maximum extent along the x-axis in font units.
	 *
	 * @return int
	 */
	public function getXMaxExtent(): int
	{
		return $this->xMaxExtent;
	}

	/**
	 * Sets the caret slope rise value.
	 *
	 * @param int $caretSlopeRise The caret slope rise.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the caret slope rise value is not valid.
	 */
	public function setCaretSlopeRise(int $caretSlopeRise): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($caretSlopeRise)) {
			throw new InvalidArgumentException("Caret Slope Rise is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->caretSlopeRise = $caretSlopeRise;
		return $this;
	}

	/**
	 * Gets the caret slope rise value.
	 *
	 * @return int
	 */
	public function getCaretSlopeRise(): int
	{
		return $this->caretSlopeRise;
	}

	/**
	 * Sets the caret slope run value.
	 *
	 * @param int $caretSlopeRun The caret slope run.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the caret slope run value is not valid.
	 */
	public function setCaretSlopeRun(int $caretSlopeRun): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($caretSlopeRun)) {
			throw new InvalidArgumentException("Caret Slope Run is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->caretSlopeRun = $caretSlopeRun;
		return $this;
	}

	/**
	 * Gets the caret slope run value.
	 *
	 * @return int
	 */
	public function getCaretSlopeRun(): int
	{
		return $this->caretSlopeRun;
	}

	/**
	 * Sets the caret offset in font units.
	 *
	 * @param int $caretOffset The caret offset.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the caret offset value is not valid.
	 */
	public function setCaretOffset(int $caretOffset): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($caretOffset)) {
			throw new InvalidArgumentException("Caret Offset is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->caretOffset = $caretOffset;
		return $this;
	}

	/**
	 * Gets the caret offset in font units.
	 *
	 * @return int
	 */
	public function getCaretOffset(): int
	{
		return $this->caretOffset;
	}

	/**
	 * Sets reserved1 value.
	 *
	 * @param int $reserved1 The reserved value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the value is invalid.
	 */
	public function setReserved1(int $reserved1): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($reserved1)) {
			throw new InvalidArgumentException("Reserved1 is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->reserved1 = $reserved1;
		return $this;
	}

	/**
	 * Gets reserved1 value.
	 *
	 * @return int
	 */
	public function getReserved1(): int
	{
		return $this->reserved1;
	}

	/**
	 * Sets reserved2 value.
	 *
	 * @param int $reserved2 The reserved value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the value is invalid.
	 */
	public function setReserved2(int $reserved2): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($reserved2)) {
			throw new InvalidArgumentException("Reserved2 is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->reserved2 = $reserved2;
		return $this;
	}

	/**
	 * Gets reserved1 value.
	 *
	 * @return int
	 */
	public function getReserved2(): int
	{
		return $this->reserved2;
	}

	/**
	 * Sets reserved3 value.
	 *
	 * @param int $reserved3 The reserved value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the value is invalid.
	 */
	public function setReserved3(int $reserved3): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($reserved3)) {
			throw new InvalidArgumentException("Reserved3 is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->reserved3 = $reserved3;
		return $this;
	}

	/**
	 * Gets reserved1 value.
	 *
	 * @return int
	 */
	public function getReserved3(): int
	{
		return $this->reserved3;
	}

	/**
	 * Sets reserved4 value.
	 *
	 * @param int $reserved4 The reserved value.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException If the value is invalid.
	 */
	public function setReserved4(int $reserved4): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($reserved4)) {
			throw new InvalidArgumentException("Reserved4 is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->reserved4 = $reserved4;
		return $this;
	}

	/**
	 * Gets reserved4 value.
	 *
	 * @return int
	 */
	public function getReserved4(): int
	{
		return $this->reserved4;
	}

	/**
	 * Sets the format of the metric data (expected to be 0).
	 *
	 * @param int $metricDataFormat The number of long horizontal metrics.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setMetricDataFormat(int $metricDataFormat): TrueTypeFontHorizontalHeaderTable
	{
		if (!IntegerValidator::isValid($metricDataFormat)) {
			throw new InvalidArgumentException("Metric data format is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->metricDataFormat = $metricDataFormat;
		return $this;
	}

	/**
	 * Gets the format of the metric data (expected to be 0).
	 *
	 * @return int
	 */
	public function getMetricDataFormat(): int
	{
		return $this->metricDataFormat;
	}

	/**
	 * Sets the number of horizontal metrics.
	 *
	 * @param int $numOfLongHorMetrics The number of long horizontal metrics.
	 * @return TrueTypeFontHorizontalHeaderTable
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setNumOfLongHorMetrics(int $numOfLongHorMetrics): TrueTypeFontHorizontalHeaderTable
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

}