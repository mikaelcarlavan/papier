<?php

namespace Papier\Font\TrueType;

use InvalidArgumentException;
use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RealValidator;

class TrueTypeFontMaxProfileTable extends TrueTypeFontTable
{
	/**
	 * Version.
	 *
	 * @var float
	 */
	protected float $version = 0;

	/**
	 * Number of glyphs.
	 *
	 * @var int
	 */
	protected int $numGlyphs = 0;

	/**
	 * Maximum points.
	 *
	 * @var int
	 */
	protected int $maxPoints = 0;

	/**
	 * Maximum contours.
	 *
	 * @var int
	 */
	protected int $maxContours = 0;

	/**
	 * Maximum composite points.
	 *
	 * @var int
	 */
	protected int $maxCompositePoints = 0;

	/**
	 * Maximum composite contours.
	 *
	 * @var int
	 */
	protected int $maxCompositeContours = 0;

	/**
	 * Maximum zones.
	 *
	 * @var int
	 */
	protected int $maxZones = 0;

	/**
	 * Maximum twilight points.
	 *
	 * @var int
	 */
	protected int $maxTwilightPoints = 0;

	/**
	 * Maximum storage.
	 *
	 * @var int
	 */
	protected int $maxStorage = 0;

	/**
	 * Maximum function definitions.
	 *
	 * @var int
	 */
	protected int $maxFunctionDefs = 0;

	/**
	 * Maximum instruction definitions.
	 *
	 * @var int
	 */
	protected int $maxInstructionDefs = 0;

	/**
	 * Maximum stack elements.
	 *
	 * @var int
	 */
	protected int $maxStackElements = 0;

	/**
	 * Maximum size of instructions.
	 *
	 * @var int
	 */
	protected int $maxSizeOfInstructions = 0;

	/**
	 * Maximum component elements.
	 *
	 * @var int
	 */
	protected int $maxComponentElements = 0;

	/**
	 * Maximum component depth.
	 *
	 * @var int
	 */
	protected int $maxComponentDepth = 0;

	/**
	 * Extract table's data
	 */
	public function parse(): void
	{
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);

		$this->setVersion($stream->unpackFixed());
		$this->setNumGlyphs($stream->unpackUnsignedShortInteger());
		$this->setMaxPoints($stream->unpackUnsignedShortInteger());
		$this->setMaxContours($stream->unpackUnsignedShortInteger());
		$this->setMaxCompositePoints($stream->unpackUnsignedShortInteger());
		$this->setMaxCompositeContours($stream->unpackUnsignedShortInteger());
		$this->setMaxZones($stream->unpackUnsignedShortInteger());
		$this->setMaxTwilightPoints($stream->unpackUnsignedShortInteger());
		$this->setMaxStorage($stream->unpackUnsignedShortInteger());
		$this->setMaxFunctionDefs($stream->unpackUnsignedShortInteger());
		$this->setMaxInstructionDefs($stream->unpackUnsignedShortInteger());
		$this->setMaxStackElements($stream->unpackUnsignedShortInteger());
		$this->setMaxSizeOfInstructions($stream->unpackUnsignedShortInteger());
		$this->setMaxComponentElements($stream->unpackUnsignedShortInteger());
		$this->setMaxComponentDepth($stream->unpackUnsignedShortInteger());
	}

	/**
	 * Set the version of the MaxP table.
	 *
	 * @param float $version
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setVersion(float $version): TrueTypeFontMaxProfileTable
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
	 * Set the number of glyphs.
	 *
	 * @param int $numGlyphs
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setNumGlyphs(int $numGlyphs): TrueTypeFontMaxProfileTable
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
	 * Set the maximum number of points.
	 *
	 * @param int $maxPoints
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxPoints(int $maxPoints): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxPoints)) {
			throw new InvalidArgumentException("Max points is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxPoints = $maxPoints;
		return $this;
	}

	/**
	 * Get the maximum number of points.
	 *
	 * @return int
	 */
	public function getMaxPoints(): int
	{
		return $this->maxPoints;
	}

	/**
	 * Set the maximum number of contours.
	 *
	 * @param int $maxContours
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxContours(int $maxContours): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxContours)) {
			throw new InvalidArgumentException("Max contours is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxContours = $maxContours;
		return $this;
	}

	/**
	 * Get the maximum number of contours.
	 *
	 * @return int
	 */
	public function getMaxContours(): int
	{
		return $this->maxContours;
	}

	/**
	 * Set the maximum number of composite points.
	 *
	 * @param int $maxCompositePoints
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxCompositePoints(int $maxCompositePoints): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxCompositePoints)) {
			throw new InvalidArgumentException("Max composite points is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxCompositePoints = $maxCompositePoints;
		return $this;
	}

	/**
	 * Get the maximum number of composite points.
	 *
	 * @return int
	 */
	public function getMaxCompositePoints(): int
	{
		return $this->maxCompositePoints;
	}

	/**
	 * Set the maximum number of composite contours.
	 *
	 * @param int $maxCompositeContours
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxCompositeContours(int $maxCompositeContours): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxCompositeContours)) {
			throw new InvalidArgumentException("Max composite contours is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxCompositeContours = $maxCompositeContours;
		return $this;
	}

	/**
	 * Get the maximum number of composite contours.
	 *
	 * @return int
	 */
	public function getMaxCompositeContours(): int
	{
		return $this->maxCompositeContours;
	}

	/**
	 * Set the maximum number of zones.
	 *
	 * @param int $maxZones
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxZones(int $maxZones): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxZones)) {
			throw new InvalidArgumentException("Max zones is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxZones = $maxZones;
		return $this;
	}

	/**
	 * Get the maximum number of zones.
	 *
	 * @return int
	 */
	public function getMaxZones(): int
	{
		return $this->maxZones;
	}

	/**
	 * Set the maximum number of twilight points.
	 *
	 * @param int $maxTwilightPoints
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxTwilightPoints(int $maxTwilightPoints): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxTwilightPoints)) {
			throw new InvalidArgumentException("Max twilight points is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxTwilightPoints = $maxTwilightPoints;
		return $this;
	}

	/**
	 * Get the maximum number of twilight points.
	 *
	 * @return int
	 */
	public function getMaxTwilightPoints(): int
	{
		return $this->maxTwilightPoints;
	}

	/**
	 * Set the maximum storage.
	 *
	 * @param int $maxStorage
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxStorage(int $maxStorage): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxStorage)) {
			throw new InvalidArgumentException("Max storage is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxStorage = $maxStorage;
		return $this;
	}

	/**
	 * Get the maximum storage.
	 *
	 * @return int
	 */
	public function getMaxStorage(): int
	{
		return $this->maxStorage;
	}

	/**
	 * Set the maximum function definitions.
	 *
	 * @param int $maxFunctionDefs
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxFunctionDefs(int $maxFunctionDefs): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxFunctionDefs)) {
			throw new InvalidArgumentException("Max function defs is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxFunctionDefs = $maxFunctionDefs;
		return $this;
	}

	/**
	 * Get the maximum function definitions.
	 *
	 * @return int
	 */
	public function getMaxFunctionDefs(): int
	{
		return $this->maxFunctionDefs;
	}

	/**
	 * Set the maximum instruction definitions.
	 *
	 * @param int $maxInstructionDefs
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxInstructionDefs(int $maxInstructionDefs): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxInstructionDefs)) {
			throw new InvalidArgumentException("Max instruction defs is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxInstructionDefs = $maxInstructionDefs;
		return $this;
	}

	/**
	 * Get the maximum instruction definitions.
	 *
	 * @return int
	 */
	public function getMaxInstructionDefs(): int
	{
		return $this->maxInstructionDefs;
	}

	/**
	 * Set the maximum stack elements.
	 *
	 * @param int $maxStackElements
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxStackElements(int $maxStackElements): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxStackElements)) {
			throw new InvalidArgumentException("Max stack elements is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxStackElements = $maxStackElements;
		return $this;
	}

	/**
	 * Get the maximum stack elements.
	 *
	 * @return int
	 */
	public function getMaxStackElements(): int
	{
		return $this->maxStackElements;
	}

	/**
	 * Set the maximum size of instructions.
	 *
	 * @param int $maxSizeOfInstructions
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxSizeOfInstructions(int $maxSizeOfInstructions): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxSizeOfInstructions)) {
			throw new InvalidArgumentException("Max size of instructions is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxSizeOfInstructions = $maxSizeOfInstructions;
		return $this;
	}

	/**
	 * Get the maximum size of instructions.
	 *
	 * @return int
	 */
	public function getMaxSizeOfInstructions(): int
	{
		return $this->maxSizeOfInstructions;
	}

	/**
	 * Set the maximum component elements.
	 *
	 * @param int $maxComponentElements
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxComponentElements(int $maxComponentElements): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxComponentElements)) {
			throw new InvalidArgumentException("Max components elements is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxComponentElements = $maxComponentElements;
		return $this;
	}

	/**
	 * Get the maximum component elements.
	 *
	 * @return int
	 */
	public function getMaxComponentElements(): int
	{
		return $this->maxComponentElements;
	}

	/**
	 * Set the maximum component depth.
	 *
	 * @param int $maxComponentDepth
	 * @return TrueTypeFontMaxProfileTable
	 */
	public function setMaxComponentDepth(int $maxComponentDepth): TrueTypeFontMaxProfileTable
	{
		if (!IntegerValidator::isValid($maxComponentDepth)) {
			throw new InvalidArgumentException("Max components depth is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->maxComponentDepth = $maxComponentDepth;
		return $this;
	}

	/**
	 * Get the maximum component depth.
	 *
	 * @return int
	 */
	public function getMaxComponentDepth(): int
	{
		return $this->maxComponentDepth;
	}
}