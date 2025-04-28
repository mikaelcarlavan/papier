<?php

namespace Papier\Font\TrueType;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RealValidator;
use InvalidArgumentException;

class TrueTypeFontPostTable extends TrueTypeFontTable
{
	/**
	 * Version.
	 *
	 * @var float
	 */
	protected float $version = 0;

	/**
	 * Italic angle
	 *
	 * @var float
	 */
	protected float $italicAngle = 0;

	/**
	 * Underline position
	 *
	 * @var int
	 */
	protected int $underlinePosition;

	/**
	 * Underline thickness
	 *
	 * @var int
	 */
	protected int $underlineThickness;

	/**
	 * Set to 0 if the font is proportionally spaced, non-zero if the font is not proportionally spaced
	 *
	 * @var int
	 */
	protected int $isFixedPitch;

	/**
	 * Minimum memory usage when an OpenType font is downloaded as a Type 1 font.
	 *
	 * @var int
	 */
	protected int $minMemType42;

	/**
	 * Maximum memory usage when an OpenType font is downloaded
	 *
	 * @var int
	 */
	protected int $maxMemType42;

	/**
	 * Minimum memory usage when an OpenType font is downloaded as a Type 1 font.
	 *
	 * @var int
	 */
	protected int $minMemType1;

	/**
	 * Maximum memory usage when an OpenType font is downloaded as a Type 1 font.
	 *
	 * @var int
	 */
	protected int $maxMemType1;


	/**
	 * Extract table's data
	 */
	public function parse(): void
	{
		/** @var TrueTypeFontFileHelper $stream */
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);

		$this->setVersion($stream->unpackFixed());

		$this->setVersion($stream->unpackFixed());
		$this->setItalicAngle($stream->unpackFixed());
		$this->setUnderlinePosition($stream->unpackFWord());
		$this->setUnderlineThickness($stream->unpackFWord());
		$this->setIsFixedPitch($stream->unpackUnsignedLongInteger());
		$this->setMinMemType42($stream->unpackUnsignedLongInteger());
		$this->setMaxMemType42($stream->unpackUnsignedLongInteger());
		$this->setMinMemType1($stream->unpackUnsignedLongInteger());
		$this->setMaxMemType1($stream->unpackUnsignedLongInteger());
	}

	/**
	 * Set the version of the MaxP table.
	 *
	 * @param float $version
	 * @return TrueTypeFontPostTable
	 */
	public function setVersion(float $version): TrueTypeFontPostTable
	{
		if (!RealValidator::isValid($version)) {
			throw new InvalidArgumentException("Version is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->version = $version;
		return $this;
	}

	/**
	 * Get the version of the MaxP table.
	 *
	 * @return float
	 */
	public function getVersion(): float
	{
		return $this->version;
	}

	/**
	 * Set underline position.
	 *
	 * @param int $underlinePosition
	 * @return TrueTypeFontPostTable
	 */
	public function setUnderlinePosition(int $underlinePosition): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($underlinePosition)) {
			throw new InvalidArgumentException("Underline position is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->underlinePosition = $underlinePosition;
		return $this;
	}

	/**
	 * Get underline position.
	 *
	 * @return int
	 */
	public function getUnderlinePosition(): int
	{
		return $this->underlinePosition;
	}

	/**
	 * Set italic angle.
	 *
	 * @param float $italicAngle
	 * @return TrueTypeFontPostTable
	 */
	public function setItalicAngle(float $italicAngle): TrueTypeFontPostTable
	{
		if (!RealValidator::isValid($italicAngle)) {
			throw new InvalidArgumentException("Italic angle is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->italicAngle = $italicAngle;
		return $this;
	}

	/**
	 * Get italic angle.
	 *
	 * @return float
	 */
	public function getItalicAngle(): float
	{
		return $this->italicAngle;
	}

	/**
	 * Set underline thickness.
	 *
	 * @param int $underlineThickness
	 * @return TrueTypeFontPostTable
	 */
	public function setUnderlineThickness(int $underlineThickness): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($underlineThickness)) {
			throw new InvalidArgumentException("Underline thickness is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->underlineThickness = $underlineThickness;
		return $this;
	}

	/**
	 * Get underline thickness.
	 *
	 * @return int
	 */
	public function getUnderlineThickness(): int
	{
		return $this->underlineThickness;
	}

	/**
	 * Set fixed pitch flag.
	 *
	 * @param int $isFixedPitch
	 * @return TrueTypeFontPostTable
	 */
	public function setIsFixedPitch(int $isFixedPitch): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($isFixedPitch)) {
			throw new InvalidArgumentException("Fixed pitch flag is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->isFixedPitch = $isFixedPitch;
		return $this;
	}

	/**
	 * Get fixed pitch flag.
	 *
	 * @return int
	 */
	public function getIsFixedPitch(): int
	{
		return $this->isFixedPitch;
	}

	/**
	 * Set minimum memory usage for Type 42 font.
	 *
	 * @param int $minMemType42
	 * @return TrueTypeFontPostTable
	 */
	public function setMinMemType42(int $minMemType42): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($minMemType42)) {
			throw new InvalidArgumentException("Minimum memory for Type 42 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->minMemType42 = $minMemType42;
		return $this;
	}

	/**
	 * Get minimum memory usage for Type 42 font.
	 *
	 * @return int
	 */
	public function getMinMemType42(): int
	{
		return $this->minMemType42;
	}

	/**
	 * Set maximum memory usage for Type 42 font.
	 *
	 * @param int $maxMemType42
	 * @return TrueTypeFontPostTable
	 */
	public function setMaxMemType42(int $maxMemType42): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($maxMemType42)) {
			throw new InvalidArgumentException("Maximum memory for Type 42 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxMemType42 = $maxMemType42;
		return $this;
	}

	/**
	 * Get maximum memory usage for Type 42 font.
	 *
	 * @return int
	 */
	public function getMaxMemType42(): int
	{
		return $this->maxMemType42;
	}

	/**
	 * Set minimum memory usage for Type 1 font.
	 *
	 * @param int $minMemType1
	 * @return TrueTypeFontPostTable
	 */
	public function setMinMemType1(int $minMemType1): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($minMemType1)) {
			throw new InvalidArgumentException("Minimum memory for Type 1 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->minMemType1 = $minMemType1;
		return $this;
	}

	/**
	 * Get minimum memory usage for Type 1 font.
	 *
	 * @return int
	 */
	public function getMinMemType1(): int
	{
		return $this->minMemType1;
	}

	/**
	 * Set maximum memory usage for Type 1 font.
	 *
	 * @param int $maxMemType1
	 * @return TrueTypeFontPostTable
	 */
	public function setMaxMemType1(int $maxMemType1): TrueTypeFontPostTable
	{
		if (!IntegerValidator::isValid($maxMemType1)) {
			throw new InvalidArgumentException("Maximum memory for Type 1 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxMemType1 = $maxMemType1;
		return $this;
	}

	/**
	 * Get maximum memory usage for Type 1 font.
	 *
	 * @return int
	 */
	public function getMaxMemType1(): int
	{
		return $this->maxMemType1;
	}
}