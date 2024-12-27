<?php

namespace Papier\Font\TrueType;
use DateTime;
use InvalidArgumentException;
use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RealValidator;

class TrueTypeFontHeadTable extends TrueTypeFontTable
{
	/**
	 * Version.
	 *
	 * @var float
	 */
	protected float $version;

	/**
	 * Font revision.
	 *
	 * @var float
	 */
	protected float $fontRevision;

	/**
	 * Checksum adjustment.
	 *
	 * @var int
	 */
	protected int $checksumAdjustment;

	/**
	 * Magic number.
	 *
	 * @var int
	 */
	protected int $magicNumber;

	/**
	 * Flags.
	 *
	 * @var int
	 */
	protected int $flags;

	/**
	 * Units per EM.
	 *
	 * @var int
	 */
	protected int $unitsPerEm;

	/**
	 * Created date.
	 *
	 * @var DateTime
	 */
	protected DateTime $created;

	/**
	 * Modified date.
	 *
	 * @var DateTime
	 */
	protected DateTime $modified;

	/**
	 * X min.
	 *
	 * @var int
	 */
	protected int $xMin;

	/**
	 * Y min.
	 *
	 * @var int
	 */
	protected int $yMin;

	/**
	 * X max.
	 *
	 * @var int
	 */
	protected int $xMax;

	/**
	 * Y max.
	 *
	 * @var int
	 */
	protected int $yMax;

	/**
	 * MAC style.
	 *
	 * @var int
	 */
	protected int $macStyle;

	/**
	 * Lowest Rec PPEM.
	 *
	 * @var int
	 */
	protected int $lowestRecPPEM;

	/**
	 * Font direction hint.
	 *
	 * @var int
	 */
	protected int $fontDirectionHint;

	/**
	 * Index to location format.
	 *
	 * @var int
	 */
	protected int $indexToLocFormat;

	/**
	 * Glyph data format.
	 *
	 * @var int
	 */
	protected int $glyphDataFormat;

	/**
	 * Extract table's data
	 *
	 */
	public function parse(): void
	{
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);

		$this->setVersion($stream->unpackFixed());
		$this->setFontRevision($stream->unpackFixed());
		$this->setChecksumAdjustment($stream->unpackUnsignedInteger());
		$this->setMagicNumber($stream->unpackUnsignedInteger());
		$this->setFlags($stream->unpackUnsignedShortInteger());
		$this->setUnitsPerEm($stream->unpackUnsignedShortInteger());
		$this->setCreated($stream->unpackDate());
		$this->setModified($stream->unpackDate());
		$this->setXMin($stream->unpackFWord());
		$this->setYMin($stream->unpackFWord());
		$this->setXMax($stream->unpackFWord());
		$this->setYMax($stream->unpackFWord());
		$this->setMacStyle($stream->unpackUnsignedShortInteger());
		$this->setLowestRecPPEM($stream->unpackUnsignedShortInteger());
		$this->setFontDirectionHint($stream->unpackShortInteger());
		$this->setIndexToLocFormat($stream->unpackShortInteger());
		$this->setGlyphDataFormat($stream->unpackShortInteger());
	}

	/**
	 * Set table's version.
	 *
	 * @param float $version
	 * @return TrueTypeFontHeadTable
	 */
	public function setVersion(float $version = 0): TrueTypeFontHeadTable
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
	 * Set table's font revision.
	 *
	 * @param float $fontRevision
	 * @return TrueTypeFontHeadTable
	 */
	public function setFontRevision(float $fontRevision = 0): TrueTypeFontHeadTable
	{
		if (!RealValidator::isValid($fontRevision)) {
			throw new InvalidArgumentException("Font revision is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->fontRevision = $fontRevision;

		return $this;
	}

	/**
	 * Get table's font revision
	 *
	 * @return float
	 */
	public function getFontRevision(): float
	{
		return $this->fontRevision;
	}

	/**
	 * Get checksum adjustment.
	 *
	 * @return int
	 */
	public function getChecksumAdjustment(): int
	{
		return $this->checksumAdjustment;
	}

	/**
	 * Set checksum adjustment.
	 *
	 * @param int $checksumAdjustment
	 * @return TrueTypeFontHeadTable
	 */
	public function setChecksumAdjustment(int $checksumAdjustment): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($checksumAdjustment)) {
			throw new InvalidArgumentException("Checksum adjustment is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->checksumAdjustment = $checksumAdjustment;

		return $this;
	}

	/**
	 * Get magic number.
	 *
	 * @return int
	 */
	public function getMagicNumber(): int
	{
		return $this->magicNumber;
	}

	/**
	 * Set magic number.
	 *
	 * @param int $magicNumber
	 * @return TrueTypeFontHeadTable
	 */
	public function setMagicNumber(int $magicNumber): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($magicNumber)) {
			throw new InvalidArgumentException("Magic number is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->magicNumber = $magicNumber;

		return $this;
	}


	/**
	 * Get flags.
	 *
	 * @return int
	 */
	public function getFlags(): int
	{
		return $this->flags;
	}

	/**
	 * Set flags.
	 *
	 * @param int $flags
	 * @return TrueTypeFontHeadTable
	 */
	public function setFlags(int $flags): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($flags)) {
			throw new InvalidArgumentException("Flags value is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->flags = $flags;

		return $this;
	}

	/**
	 * Get units per EM.
	 *
	 * @return int
	 */
	public function getUnitsPerEm(): int
	{
		return $this->unitsPerEm;
	}

	/**
	 * Set units per EM.
	 *
	 * @param int $unitsPerEm
	 * @return TrueTypeFontHeadTable
	 */
	public function setUnitsPerEm(int $unitsPerEm): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($unitsPerEm, 0, 16384)) {
			throw new InvalidArgumentException("Units per EM must be between 1 and 16384. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->unitsPerEm = $unitsPerEm;

		return $this;
	}

	/**
	 * Get created date.
	 *
	 * @return DateTime
	 */
	public function getCreated(): DateTime
	{
		return $this->created;
	}

	/**
	 * Set created date.
	 *
	 * @param DateTime $created
	 * @return TrueTypeFontHeadTable
	 */
	public function setCreated(DateTime $created): TrueTypeFontHeadTable
	{
		$this->created = $created;

		return $this;
	}

	/**
	 * Get modified date.
	 *
	 * @return DateTime
	 */
	public function getModified(): DateTime
	{
		return $this->modified;
	}

	/**
	 * Set modified date.
	 *
	 * @param DateTime $modified
	 * @return TrueTypeFontHeadTable
	 */
	public function setModified(DateTime $modified): TrueTypeFontHeadTable
	{
		$this->modified = $modified;

		return $this;
	}

	/**
	 * Get xMin.
	 *
	 * @return int
	 */
	public function getXMin(): int
	{
		return $this->xMin;
	}

	/**
	 * Set xMin.
	 *
	 * @param int $xMin
	 * @return TrueTypeFontHeadTable
	 */
	public function setXMin(int $xMin): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($xMin)) {
			throw new InvalidArgumentException("xMin value is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->xMin = $xMin;

		return $this;
	}

	/**
	 * Get yMin.
	 *
	 * @return int
	 */
	public function getYMin(): int
	{
		return $this->yMin;
	}

	/**
	 * Set yMin.
	 *
	 * @param int $yMin
	 * @return TrueTypeFontHeadTable
	 */
	public function setYMin(int $yMin): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($yMin)) {
			throw new InvalidArgumentException("yMin value is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->yMin = $yMin;

		return $this;
	}

	/**
	 * Get xMax.
	 *
	 * @return int
	 */
	public function getXMax(): int
	{
		return $this->xMax;
	}

	/**
	 * Set xMax.
	 *
	 * @param int $xMax
	 * @return TrueTypeFontHeadTable
	 */
	public function setXMax(int $xMax): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($xMax)) {
			throw new InvalidArgumentException("xMax value is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->xMax = $xMax;

		return $this;
	}

	/**
	 * Get yMax.
	 *
	 * @return int
	 */
	public function getYMax(): int
	{
		return $this->yMax;
	}

	/**
	 * Set yMax.
	 *
	 * @param int $yMax
	 * @return TrueTypeFontHeadTable
	 */
	public function setYMax(int $yMax): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($yMax)) {
			throw new InvalidArgumentException("yMax value is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->yMax = $yMax;

		return $this;
	}

	/**
	 * Get MAC style.
	 *
	 * @return int
	 */
	public function getMacStyle(): int
	{
		return $this->macStyle;
	}

	/**
	 * Set MAC style.
	 *
	 * @param int $macStyle
	 * @return TrueTypeFontHeadTable
	 */
	public function setMacStyle(int $macStyle): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($macStyle)) {
			throw new InvalidArgumentException("MAC style value is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->macStyle = $macStyle;

		return $this;
	}

	/**
	 * Get lowest recommended PPEM.
	 *
	 * @return int
	 */
	public function getLowestRecPPEM(): int
	{
		return $this->lowestRecPPEM;
	}

	/**
	 * Set lowest recommended PPEM.
	 *
	 * @param int $lowestRecPPEM
	 * @return TrueTypeFontHeadTable
	 */
	public function setLowestRecPPEM(int $lowestRecPPEM): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($lowestRecPPEM)) {
			throw new InvalidArgumentException("Lowest recommended PPEM is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->lowestRecPPEM = $lowestRecPPEM;

		return $this;
	}

	/**
	 * Get font direction hint.
	 *
	 * @return int
	 */
	public function getFontDirectionHint(): int
	{
		return $this->fontDirectionHint;
	}

	/**
	 * Set font direction hint.
	 *
	 * @param int $fontDirectionHint
	 * @return TrueTypeFontHeadTable
	 */
	public function setFontDirectionHint(int $fontDirectionHint): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($fontDirectionHint)) {
			throw new InvalidArgumentException("Font direction hint is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->fontDirectionHint = $fontDirectionHint;

		return $this;
	}

	/**
	 * Get index to location format.
	 *
	 * @return int
	 */
	public function getIndexToLocFormat(): int
	{
		return $this->indexToLocFormat;
	}

	/**
	 * Set index to location format.
	 *
	 * @param int $indexToLocFormat
	 * @return TrueTypeFontHeadTable
	 */
	public function setIndexToLocFormat(int $indexToLocFormat): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($indexToLocFormat)) {
			throw new InvalidArgumentException("Index to location format is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->indexToLocFormat = $indexToLocFormat;

		return $this;
	}

	/**
	 * Get glyph data format.
	 *
	 * @return int
	 */
	public function getGlyphDataFormat(): int
	{
		return $this->glyphDataFormat;
	}

	/**
	 * Set glyph data format.
	 *
	 * @param int $glyphDataFormat
	 * @return TrueTypeFontHeadTable
	 */
	public function setGlyphDataFormat(int $glyphDataFormat): TrueTypeFontHeadTable
	{
		if (!IntegerValidator::isValid($glyphDataFormat)) {
			throw new InvalidArgumentException("Glyph data format is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->glyphDataFormat = $glyphDataFormat;

		return $this;
	}

}