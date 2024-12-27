<?php

namespace Papier\Font;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RealValidator;

use InvalidArgumentException;
use Papier\Validator\StringValidator;

class TrueTypeFontOS2Table extends TrueTypeFontTable
{
	/**
	 * The version of the OS/2 table.
	 *
	 * @var int
	 */
	protected int $version;

	/**
	 * The average character width.
	 *
	 * @var int
	 */
	protected int $xAvgCharWidth;

	/**
	 * The weight class of the font.
	 *
	 * @var int
	 */
	protected int $usWeightClass;

	/**
	 * The width class of the font.
	 *
	 * @var int
	 */
	protected int $usWidthClass;

	/**
	 * The fsType of the font.
	 *
	 * @var int
	 */
	protected int $fsType;

	/**
	 * The subscript X size.
	 *
	 * @var int
	 */
	protected int $ySubscriptXSize;

	/**
	 * The subscript Y size.
	 *
	 * @var int
	 */
	protected int $ySubscriptYSize;

	/**
	 * The subscript X offset.
	 *
	 * @var int
	 */
	protected int $ySubscriptXOffset;

	/**
	 * The subscript Y offset.
	 *
	 * @var int
	 */
	protected int $ySubscriptYOffset;

	/**
	 * The superscript X size.
	 *
	 * @var int
	 */
	protected int $ySuperscriptXSize;

	/**
	 * The superscript Y size.
	 *
	 * @var int
	 */
	protected int $ySuperscriptYSize;

	/**
	 * The superscript X offset.
	 *
	 * @var int
	 */
	protected int $ySuperscriptXOffset;

	/**
	 * The superscript Y offset.
	 *
	 * @var int
	 */
	protected int $ySuperscriptYOffset;

	/**
	 * The strikeout size.
	 *
	 * @var int
	 */
	protected int $yStrikeoutSize;

	/**
	 * The strikeout position.
	 *
	 * @var int
	 */
	protected int $yStrikeoutPosition;

	/**
	 * The family class.
	 *
	 * @var int
	 */
	protected int $sFamilyClass;

	/**
	 * The PANOSE font family classification.
	 *
	 * @var string
	 */
	protected string $panose;

	/**
	 * Unicode range 1.
	 *
	 * @var int
	 */
	protected int $ulUnicodeRange1;

	/**
	 * Unicode range 2.
	 *
	 * @var int
	 */
	protected int $ulUnicodeRange2;

	/**
	 * Unicode range 3.
	 *
	 * @var int
	 */
	protected int $ulUnicodeRange3;

	/**
	 * Unicode range 4.
	 *
	 * @var int
	 */
	protected int $ulUnicodeRange4;

	/**
	 * Vendor ID string.
	 *
	 * @var string
	 */
	protected string $achVendID;

	/**
	 * Font selection flags.
	 *
	 * @var int
	 */
	protected int $fsSelection;

	/**
	 * First character index.
	 *
	 * @var int
	 */
	protected int $fsFirstCharIndex;

	/**
	 * Last character index.
	 *
	 * @var int
	 */
	protected int $fsLastCharIndex;

	/**
	 * Typographic ascender.
	 *
	 * @var int
	 */
	protected int $sTypoAscender;

	/**
	 * Typographic descender.
	 *
	 * @var int
	 */
	protected int $sTypoDescender;

	/**
	 * Typographic line gap.
	 *
	 * @var int
	 */
	protected int $sTypoLineGap;

	/**
	 * Windows ascent value.
	 *
	 * @var int
	 */
	protected int $usWinAscent;

	/**
	 * Windows descent value.
	 *
	 * @var int
	 */
	protected int $usWinDescent;

	/**
	 * Unicode codepage range 1.
	 *
	 * @var int
	 */
	protected int $ulCodePageRange1;

	/**
	 * Unicode codepage range 2.
	 *
	 * @var int
	 */
	protected int $ulCodePageRange2;

	/**
	 * SX height.
	 *
	 * @var int
	 */
	protected int $sxHeight;

	/**
	 * Cap height.
	 *
	 * @var int
	 */
	protected int $sCapHeight;

	/**
	 * Default character.
	 *
	 * @var int
	 */
	protected int $usDefaultChar;

	/**
	 * Break character.
	 *
	 * @var int
	 */
	protected int $usBreakChar;

	/**
	 * Max context.
	 *
	 * @var int
	 */
	protected int $usMaxContext;

	/**
	 * Lower point size.
	 *
	 * @var int
	 */
	protected int $usLowerPointSize;

	/**
	 * Upper point size.
	 *
	 * @var int
	 */
	protected int $usUpperPointSize;

	/**
	 * Extract table's data
	 *
	 */
	public function parse(): void
	{
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);


		$this->setVersion($stream->unpackUnsignedShortInteger());
		$this->setXAvgCharWidth($stream->unpackFWord());
		$this->setUsWeightClass($stream->unpackUnsignedShortInteger());
		$this->setUsWidthClass($stream->unpackUnsignedShortInteger());

		$this->setFsType($stream->unpackShortInteger());
		$this->setYSubscriptXSize($stream->unpackFWord());
		$this->setYSubscriptYSize($stream->unpackFWord());
		$this->setYSubscriptXOffset($stream->unpackFWord());
		$this->setYSubscriptYOffset($stream->unpackFWord());

		$this->setYSuperscriptXSize($stream->unpackFWord());
		$this->setYSuperscriptYSize($stream->unpackFWord());
		$this->setYSuperscriptXOffset($stream->unpackFWord());
		$this->setYSuperscriptYOffset($stream->unpackFWord());

		$this->setYStrikeoutSize($stream->unpackFWord());
		$this->setYStrikeoutPosition($stream->unpackFWord());

		$this->setSFamilyClass($stream->unpackShortInteger());
		$this->setPanose($stream->unpackString(10));
		$this->setUlUnicodeRange1($stream->unpackUnsignedInteger());
		$this->setUlUnicodeRange2($stream->unpackUnsignedInteger());
		$this->setUlUnicodeRange3($stream->unpackUnsignedInteger());
		$this->setUlUnicodeRange4($stream->unpackUnsignedInteger());

		$this->setAchVendID($stream->unpackString(4));
		$this->setFsSelection($stream->unpackUnsignedShortInteger());
		$this->setFsFirstCharIndex($stream->unpackUnsignedShortInteger());
		$this->setFsLastCharIndex($stream->unpackUnsignedShortInteger());

		$this->setSTypoAscender($stream->unpackFWord());
		$this->setSTypoDescender($stream->unpackFWord());
		$this->setSTypoLineGap($stream->unpackFWord());

		$this->setUsWinAscent($stream->unpackUnsignedFWord());
		$this->setUsWinDescent($stream->unpackUnsignedFWord());

		if ($this->getVersion() > 0) {
			$this->setUlCodePageRange1($stream->unpackUnsignedInteger());
			$this->setUlCodePageRange2($stream->unpackUnsignedInteger());
		}

		if ($this->getVersion() > 1) {
			$this->setSxHeight($stream->unpackFWord());
			$this->setSCapHeight($stream->unpackFWord());
			$this->setUsDefaultChar($stream->unpackUnsignedShortInteger());
			$this->setUsBreakChar($stream->unpackUnsignedShortInteger());
			$this->setUsMaxContext($stream->unpackUnsignedShortInteger());
		}

		if ($this->getVersion() > 4) {
			$this->setUsLowerPointSize($stream->unpackUnsignedShortInteger());
			$this->setUsUpperPointSize($stream->unpackUnsignedShortInteger());
		}

	}

	/**
	 * Sets the version of the OS/2 table.
	 *
	 * @param int $version The version number.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the version is not valid.
	 */
	public function setVersion(int $version): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($version)) {
			throw new InvalidArgumentException("Version is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->version = $version;
		return $this;
	}

	/**
	 * Gets the version of the OS/2 table.
	 *
	 * @return int
	 */
	public function getVersion(): int
	{
		return $this->version;
	}

	/**
	 * Sets the average character width.
	 *
	 * @param int $xAvgCharWidth The average character width.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setXAvgCharWidth(int $xAvgCharWidth): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($xAvgCharWidth)) {
			throw new InvalidArgumentException("X Average Char Width is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->xAvgCharWidth = $xAvgCharWidth;
		return $this;
	}

	/**
	 * Gets the average character width.
	 *
	 * @return int
	 */
	public function getXAvgCharWidth(): int
	{
		return $this->xAvgCharWidth;
	}

	/**
	 * Sets the weight class.
	 *
	 * @param int $usWeightClass The weight class.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsWeightClass(int $usWeightClass): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usWeightClass)) {
			throw new InvalidArgumentException("Us Weight Class is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usWeightClass = $usWeightClass;
		return $this;
	}

	/**
	 * Gets the weight class.
	 *
	 * @return int
	 */
	public function getUsWeightClass(): int
	{
		return $this->usWeightClass;
	}

	/**
	 * Sets the width class.
	 *
	 * @param int $usWidthClass The width class.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsWidthClass(int $usWidthClass): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usWidthClass)) {
			throw new InvalidArgumentException("Us Width Class is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usWidthClass = $usWidthClass;
		return $this;
	}

	/**
	 * Gets the width class.
	 *
	 * @return int
	 */
	public function getUsWidthClass(): int
	{
		return $this->usWidthClass;
	}

	/**
	 * Sets the fsType value.
	 *
	 * @param int $fsType The fsType value.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setFsType(int $fsType): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($fsType)) {
			throw new InvalidArgumentException("Fs Type is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->fsType = $fsType;
		return $this;
	}

	/**
	 * Gets the fsType value.
	 *
	 * @return int
	 */
	public function getFsType(): int
	{
		return $this->fsType;
	}

	/**
	 * Sets the subscript X size.
	 *
	 * @param int $ySubscriptXSize The subscript X size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSubscriptXSize(int $ySubscriptXSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySubscriptXSize)) {
			throw new InvalidArgumentException("Y Subscript X Size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySubscriptXSize = $ySubscriptXSize;
		return $this;
	}

	/**
	 * Gets the subscript X size.
	 *
	 * @return int
	 */
	public function getYSubscriptXSize(): int
	{
		return $this->ySubscriptXSize;
	}

	
	/**
	 * Sets the Y Subscript Y size.
	 *
	 * @param int $ySubscriptYSize The Y Subscript Y size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSubscriptYSize(int $ySubscriptYSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySubscriptYSize)) {
			throw new InvalidArgumentException("Y Subscript Y size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySubscriptYSize = $ySubscriptYSize;
		return $this;
	}

	/**
	 * Gets the Y Subscript Y size.
	 *
	 * @return int
	 */
	public function getYSubscriptYSize(): int
	{
		return $this->ySubscriptYSize;
	}

	/**
	 * Sets the Y Subscript X offset.
	 *
	 * @param int $ySubscriptXOffset The Y Subscript X offset.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSubscriptXOffset(int $ySubscriptXOffset): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySubscriptXOffset)) {
			throw new InvalidArgumentException("Y Subscript X offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySubscriptXOffset = $ySubscriptXOffset;
		return $this;
	}

	/**
	 * Gets the Y Subscript X offset.
	 *
	 * @return int
	 */
	public function getYSubscriptXOffset(): int
	{
		return $this->ySubscriptXOffset;
	}

	/**
	 * Sets the Y Subscript Y offset.
	 *
	 * @param int $ySubscriptYOffset The Y Subscript Y offset.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSubscriptYOffset(int $ySubscriptYOffset): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySubscriptYOffset)) {
			throw new InvalidArgumentException("Y Subscript Y offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySubscriptYOffset = $ySubscriptYOffset;
		return $this;
	}

	/**
	 * Gets the Y Subscript Y offset.
	 *
	 * @return int
	 */
	public function getYSubscriptYOffset(): int
	{
		return $this->ySubscriptYOffset;
	}

	/**
	 * Sets the Y strikeout size.
	 *
	 * @param int $yStrikeoutSize The Y strikeout size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYStrikeoutSize(int $yStrikeoutSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($yStrikeoutSize)) {
			throw new InvalidArgumentException("Y strikeout size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->yStrikeoutSize = $yStrikeoutSize;
		return $this;
	}

	/**
	 * Gets the Y strikeout size.
	 *
	 * @return int
	 */
	public function getYStrikeoutSize(): int
	{
		return $this->yStrikeoutSize;
	}

	/**
	 * Sets the Y strikeout position.
	 *
	 * @param int $yStrikeoutPosition The Y strikeout position.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYStrikeoutPosition(int $yStrikeoutPosition): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($yStrikeoutPosition)) {
			throw new InvalidArgumentException("Y strikeout position is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->yStrikeoutPosition = $yStrikeoutPosition;
		return $this;
	}

	/**
	 * Gets the Y strikeout position.
	 *
	 * @return int
	 */
	public function getYStrikeoutPosition(): int
	{
		return $this->yStrikeoutPosition;
	}

	/**
	 * Sets the family class.
	 *
	 * @param int $sFamilyClass The family class.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setSFamilyClass(int $sFamilyClass): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($sFamilyClass)) {
			throw new InvalidArgumentException("Family class is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->sFamilyClass = $sFamilyClass;
		return $this;
	}

	/**
	 * Gets the family class.
	 *
	 * @return int
	 */
	public function getSFamilyClass(): int
	{
		return $this->sFamilyClass;
	}

	/**
	 * Sets the Panose classification.
	 *
	 * @param string $panose The Panose classification (must be a 10-character string).
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setPanose(string $panose): TrueTypeFontOS2Table
	{
		if (!StringValidator::isValid($panose)) {
			throw new InvalidArgumentException("Panose is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->panose = $panose;
		return $this;
	}

	/**
	 * Gets the Panose classification.
	 *
	 * @return string|null
	 */
	public function getPanose(): ?string
	{
		return $this->panose;
	}

	/**
	 * Sets the Y superscript X size.
	 *
	 * @param int $ySuperscriptXSize The Y superscript X size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSuperscriptXSize(int $ySuperscriptXSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySuperscriptXSize)) {
			throw new InvalidArgumentException("Y superscript X size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySuperscriptXSize = $ySuperscriptXSize;
		return $this;
	}

	/**
	 * Gets the Y superscript X size.
	 *
	 * @return int
	 */
	public function getYSuperscriptXSize(): int
	{
		return $this->ySuperscriptXSize;
	}

	/**
	 * Sets the Y superscript Y size.
	 *
	 * @param int $ySuperscriptYSize The Y superscript Y size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSuperscriptYSize(int $ySuperscriptYSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySuperscriptYSize)) {
			throw new InvalidArgumentException("Y superscript Y size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySuperscriptYSize = $ySuperscriptYSize;
		return $this;
	}

	/**
	 * Gets the Y superscript Y size.
	 *
	 * @return int
	 */
	public function getYSuperscriptYSize(): int
	{
		return $this->ySuperscriptYSize;
	}

	/**
	 * Sets the Y superscript X offset.
	 *
	 * @param int $ySuperscriptXOffset The Y superscript X offset.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSuperscriptXOffset(int $ySuperscriptXOffset): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySuperscriptXOffset)) {
			throw new InvalidArgumentException("Y superscript X offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySuperscriptXOffset = $ySuperscriptXOffset;
		return $this;
	}

	/**
	 * Gets the Y superscript X offset.
	 *
	 * @return int
	 */
	public function getYSuperscriptXOffset(): int
	{
		return $this->ySuperscriptXOffset;
	}

	/**
	 * Sets the Y superscript Y offset.
	 *
	 * @param int $ySuperscriptYOffset The Y superscript Y offset.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setYSuperscriptYOffset(int $ySuperscriptYOffset): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ySuperscriptYOffset)) {
			throw new InvalidArgumentException("Y superscript Y offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ySuperscriptYOffset = $ySuperscriptYOffset;
		return $this;
	}

	/**
	 * Gets the Y superscript Y offset.
	 *
	 * @return int
	 */
	public function getYSuperscriptYOffset(): int
	{
		return $this->ySuperscriptYOffset;
	}

	/**
	 * Sets the first Unicode range.
	 *
	 * @param int $ulUnicodeRange1 The first Unicode range.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUlUnicodeRange1(int $ulUnicodeRange1): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ulUnicodeRange1)) {
			throw new InvalidArgumentException("Unicode range 1 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ulUnicodeRange1 = $ulUnicodeRange1;
		return $this;
	}

	/**
	 * Gets the first Unicode range.
	 *
	 * @return int
	 */
	public function getUlUnicodeRange1(): int
	{
		return $this->ulUnicodeRange1;
	}

	/**
	 * Sets the second Unicode range.
	 *
	 * @param int $ulUnicodeRange2 The second Unicode range.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUlUnicodeRange2(int $ulUnicodeRange2): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ulUnicodeRange2)) {
			throw new InvalidArgumentException("Unicode range 2 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ulUnicodeRange2 = $ulUnicodeRange2;
		return $this;
	}

	/**
	 * Gets the second Unicode range.
	 *
	 * @return int
	 */
	public function getUlUnicodeRange2(): int
	{
		return $this->ulUnicodeRange2;
	}

	/**
	 * Sets the third Unicode range.
	 *
	 * @param int $ulUnicodeRange3 The third Unicode range.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUlUnicodeRange3(int $ulUnicodeRange3): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ulUnicodeRange3)) {
			throw new InvalidArgumentException("Unicode range 3 is not valid. See ".__CLASS__." class's documentation for possible values. ");
		}
		$this->ulUnicodeRange3 = $ulUnicodeRange3;
		return $this;
	}

	/**
	 * Gets the third Unicode range.
	 *
	 * @return int
	 */
	public function getUlUnicodeRange3(): int
	{
		return $this->ulUnicodeRange3;
	}

	/**
	 * Sets the fourth Unicode range.
	 *
	 * @param int $ulUnicodeRange4 The fourth Unicode range.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUlUnicodeRange4(int $ulUnicodeRange4): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ulUnicodeRange4)) {
			throw new InvalidArgumentException("Unicode range 4 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ulUnicodeRange4 = $ulUnicodeRange4;
		return $this;
	}

	/**
	 * Gets the fourth Unicode range.
	 *
	 * @return int
	 */
	public function getUlUnicodeRange4(): int
	{
		return $this->ulUnicodeRange4;
	}

	/**
	 * Sets the vendor ID.
	 *
	 * @param string $achVendID The vendor ID (must be a 4-character string).
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setAchVendID(string $achVendID): TrueTypeFontOS2Table
	{
		if (!StringValidator::isValid($achVendID)) {
			throw new InvalidArgumentException("Vendor ID not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->achVendID = $achVendID;
		return $this;
	}

	/**
	 * Gets the vendor ID.
	 *
	 * @return string|null
	 */
	public function getAchVendID(): ?string
	{
		return $this->achVendID;
	}

	/**
	 * Sets the fsSelection field.
	 *
	 * @param int $fsSelection The fsSelection value.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setFsSelection(int $fsSelection): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($fsSelection)) {
			throw new InvalidArgumentException("fsSelection is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->fsSelection = $fsSelection;
		return $this;
	}

	/**
	 * Gets the fsSelection value.
	 *
	 * @return int
	 */
	public function getFsSelection(): int
	{
		return $this->fsSelection;
	}

	/**
	 * Sets the first character index.
	 *
	 * @param int $fsFirstCharIndex The first character index.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setFsFirstCharIndex(int $fsFirstCharIndex): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($fsFirstCharIndex)) {
			throw new InvalidArgumentException("First character index is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->fsFirstCharIndex = $fsFirstCharIndex;
		return $this;
	}

	/**
	 * Gets the first character index.
	 *
	 * @return int
	 */
	public function getFsFirstCharIndex(): int
	{
		return $this->fsFirstCharIndex;
	}

	/**
	 * Sets the last character index.
	 *
	 * @param int $fsLastCharIndex The last character index.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setFsLastCharIndex(int $fsLastCharIndex): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($fsLastCharIndex)) {
			throw new InvalidArgumentException("Last character index is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->fsLastCharIndex = $fsLastCharIndex;
		return $this;
	}

	/**
	 * Gets the last character index.
	 *
	 * @return int
	 */
	public function getFsLastCharIndex(): int
	{
		return $this->fsLastCharIndex;
	}

	/**
	 * Sets the typo ascender.
	 *
	 * @param int $sTypoAscender The typo ascender.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setSTypoAscender(int $sTypoAscender): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($sTypoAscender)) {
			throw new InvalidArgumentException("Typo ascender is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->sTypoAscender = $sTypoAscender;
		return $this;
	}

	/**
	 * Gets the typo ascender.
	 *
	 * @return int
	 */
	public function getSTypoAscender(): int
	{
		return $this->sTypoAscender;
	}

	/**
	 * Sets the typo descender.
	 *
	 * @param int $sTypoDescender The typo descender.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setSTypoDescender(int $sTypoDescender): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($sTypoDescender)) {
			throw new InvalidArgumentException("Typo descender is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->sTypoDescender = $sTypoDescender;
		return $this;
	}

	/**
	 * Gets the typo descender.
	 *
	 * @return int
	 */
	public function getSTypoDescender(): int
	{
		return $this->sTypoDescender;
	}

	/**
	 * Sets the typo line gap.
	 *
	 * @param int $sTypoLineGap The typo line gap.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setSTypoLineGap(int $sTypoLineGap): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($sTypoLineGap)) {
			throw new InvalidArgumentException("Typo line gap is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->sTypoLineGap = $sTypoLineGap;
		return $this;
	}

	/**
	 * Gets the typo line gap.
	 *
	 * @return int
	 */
	public function getSTypoLineGap(): int
	{
		return $this->sTypoLineGap;
	}


	/**
	 * Sets the Windows ascent.
	 *
	 * @param int $usWinAscent The Windows ascent value.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsWinAscent(int $usWinAscent): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usWinAscent)) {
			throw new InvalidArgumentException("Windows ascent is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usWinAscent = $usWinAscent;
		return $this;
	}

	/**
	 * Gets the Windows ascent.
	 *
	 * @return int
	 */
	public function getUsWinAscent(): int
	{
		return $this->usWinAscent;
	}

	/**
	 * Sets the Windows descent.
	 *
	 * @param int $usWinDescent The Windows descent value.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsWinDescent(int $usWinDescent): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usWinDescent)) {
			throw new InvalidArgumentException("Windows descent is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usWinDescent = $usWinDescent;
		return $this;
	}

	/**
	 * Gets the Windows descent.
	 *
	 * @return int
	 */
	public function getUsWinDescent(): int
	{
		return $this->usWinDescent;
	}

	
	/**
	 * Sets the Unicode codepage range 1.
	 *
	 * @param int $ulCodePageRange1 The Unicode codepage range 1.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUlCodePageRange1(int $ulCodePageRange1): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ulCodePageRange1)) {
			throw new InvalidArgumentException("Unicode CodePage Range 1 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ulCodePageRange1 = $ulCodePageRange1;
		return $this;
	}

	/**
	 * Gets the Unicode codepage range 1.
	 *
	 * @return int
	 */
	public function getUlCodePageRange1(): int
	{
		return $this->ulCodePageRange1;
	}

	/**
	 * Sets the Unicode codepage range 2.
	 *
	 * @param int $ulCodePageRange2 The Unicode codepage range 2.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUlCodePageRange2(int $ulCodePageRange2): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($ulCodePageRange2)) {
			throw new InvalidArgumentException("Unicode CodePage Range 2 is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->ulCodePageRange2 = $ulCodePageRange2;
		return $this;
	}

	/**
	 * Gets the Unicode codepage range 2.
	 *
	 * @return int
	 */
	public function getUlCodePageRange2(): int
	{
		return $this->ulCodePageRange2;
	}

	/**
	 * Sets the SX height.
	 *
	 * @param int $sxHeight The SX height.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setSxHeight(int $sxHeight): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($sxHeight)) {
			throw new InvalidArgumentException("SX Height is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->sxHeight = $sxHeight;
		return $this;
	}

	/**
	 * Gets the SX height.
	 *
	 * @return int
	 */
	public function getSxHeight(): int
	{
		return $this->sxHeight;
	}

	/**
	 * Sets the Cap height.
	 *
	 * @param int $sCapHeight The Cap height.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setSCapHeight(int $sCapHeight): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($sCapHeight)) {
			throw new InvalidArgumentException("Cap Height is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->sCapHeight = $sCapHeight;
		return $this;
	}

	/**
	 * Gets the Cap height.
	 *
	 * @return int
	 */
	public function getSCapHeight(): int
	{
		return $this->sCapHeight;
	}

	/**
	 * Sets the Default character.
	 *
	 * @param int $usDefaultChar The Default character index.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsDefaultChar(int $usDefaultChar): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usDefaultChar)) {
			throw new InvalidArgumentException("Us Default Char is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usDefaultChar = $usDefaultChar;
		return $this;
	}

	/**
	 * Gets the Default character.
	 *
	 * @return int
	 */
	public function getUsDefaultChar(): int
	{
		return $this->usDefaultChar;
	}

	/**
	 * Sets the Break character.
	 *
	 * @param int $usBreakChar The Break character index.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsBreakChar(int $usBreakChar): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usBreakChar)) {
			throw new InvalidArgumentException("Us Break Char is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usBreakChar = $usBreakChar;
		return $this;
	}

	/**
	 * Gets the Break character.
	 *
	 * @return int
	 */
	public function getUsBreakChar(): int
	{
		return $this->usBreakChar;
	}

	/**
	 * Sets the Max context.
	 *
	 * @param int $usMaxContext The Max context.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsMaxContext(int $usMaxContext): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usMaxContext)) {
			throw new InvalidArgumentException("Us Max Context is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usMaxContext = $usMaxContext;
		return $this;
	}

	/**
	 * Gets the Max context.
	 *
	 * @return int
	 */
	public function getUsMaxContext(): int
	{
		return $this->usMaxContext;
	}

	/**
	 * Sets the Lower point size.
	 *
	 * @param int $usLowerPointSize The Lower point size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsLowerPointSize(int $usLowerPointSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usLowerPointSize)) {
			throw new InvalidArgumentException("Us Lower Point Size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usLowerPointSize = $usLowerPointSize;
		return $this;
	}

	/**
	 * Gets the Lower point size.
	 *
	 * @return int
	 */
	public function getUsLowerPointSize(): int
	{
		return $this->usLowerPointSize;
	}

	/**
	 * Sets the Upper point size.
	 *
	 * @param int $usUpperPointSize The Upper point size.
	 * @return TrueTypeFontOS2Table
	 * @throws InvalidArgumentException If the value is not valid.
	 */
	public function setUsUpperPointSize(int $usUpperPointSize): TrueTypeFontOS2Table
	{
		if (!IntegerValidator::isValid($usUpperPointSize)) {
			throw new InvalidArgumentException("Us Upper Point Size is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->usUpperPointSize = $usUpperPointSize;
		return $this;
	}

	/**
	 * Gets the Upper point size.
	 *
	 * @return int
	 */
	public function getUsUpperPointSize(): int
	{
		return $this->usUpperPointSize;
	}
}