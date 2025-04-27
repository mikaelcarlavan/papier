<?php

namespace Papier\Font\TrueType;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Validator\ArrayValidator;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;
use RuntimeException;

class TrueTypeFontCharacterToGlyphIndexMappingTable extends TrueTypeFontTable
{
	/**
	 * The version of the name table.
	 *
	 * @var int
	 */
	protected int $version;

	/**
	 * Number of name records.
	 *
	 * @var int
	 */
	protected int $count;

	/**
	 * Glyph index map.
	 *
	 * @var array
	 */
	protected array $glyphIndexMap;

	/**
	 * Set glyph index map.
	 *
	 * @param array $glyphIndexMap
	 * @return TrueTypeFontCharacterToGlyphIndexMappingTable
	 */
	public function setGlyphIndexMap(array $glyphIndexMap): TrueTypeFontCharacterToGlyphIndexMappingTable
	{
		if (!ArrayValidator::isValid($glyphIndexMap)) {
			throw new InvalidArgumentException("Map is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->glyphIndexMap = $glyphIndexMap;
		return $this;
	}

	/**
	 * Get glyph index map.
	 *
	 * @return array
	 */
	public function getGlyphIndexMap(): array
	{
		return $this->glyphIndexMap;
	}

	/**
	 * Extract table's data
	 *
	 */
	public function parse(): void
	{
		/** @var TrueTypeFontFileHelper $stream */
		$stream = $this->getHelper();

		$offset = $this->getOffset();
		$stream->setOffset($offset);


		$this->setVersion($stream->unpackUnsignedShortInteger());
		$this->setCount($stream->unpackUnsignedShortInteger());

		$count = $this->getCount();
		$encodingRecords = [];
		$glyphIndexMap = [];

		for ($i = 0; $i < $count; $i++) {
			$encodingRecords[] = [
				'platformID' => $stream->unpackUnsignedShortInteger(),
				'encodingID' => $stream->unpackUnsignedShortInteger(),
				'offset' => $stream->unpackOffset32(),
			];
		}

		$selectedOffset = -1;

		for ($i = 0; $i < $count; $i++) {
			$platformID = $encodingRecords[$i]['platformID'];
			$encodingID = $encodingRecords[$i]['encodingID'];
			$offset = $encodingRecords[$i]['offset'];

			$isWindowsPlatform = (
				$platformID === 3 &&
				($encodingID === 0 || $encodingID === 1 || $encodingID === 10)
			);

			$isUnicodePlatform = (
				$platformID === 0 &&
				in_array($encodingID, [0, 1, 2, 3, 4])
			);

			if ($isWindowsPlatform || $isUnicodePlatform) {
				$selectedOffset = $offset;
				break;
			}
		}

		if ($selectedOffset === -1) {
			throw new RuntimeException("Platform or encoding not found. See ".__CLASS__." class's documentation for possible values.");
		}

		$format = $stream->unpackUnsignedShortInteger();

		if ($format === 4) {
			$form = $this->parseFormat4($stream);
			$glyphIndexMap = $form['glyphIndexMap'];
		} else {
			throw new RuntimeException("Format is not supported. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->setGlyphIndexMap($glyphIndexMap);
	}

	/**
	 * Extract Format 4 data
	 *
	 * @param TrueTypeFontFileHelper $stream
	 * @return array
	 */
	public function parseFormat4(TrueTypeFontFileHelper $stream): array
	{
		$format = [
			'format' => 4,
			'length' => $stream->unpackUnsignedShortInteger(),
			'language' => $stream->unpackUnsignedShortInteger(),
			'segCountX2' => $stream->unpackUnsignedShortInteger(),
			'searchRange' => $stream->unpackUnsignedShortInteger(),
			'entrySelector' => $stream->unpackUnsignedShortInteger(),
			'rangeShift' => $stream->unpackUnsignedShortInteger(),
			'endCode' => [],
			'startCode' => [],
			'idDelta' => [],
			'idRangeOffset' => [],
			'glyphIndexMap' => [],
		];

		$segCount = $format['segCountX2'] >> 1;

		for ($i = 0; $i < $segCount; $i++) {
			$format['endCode'][] = $stream->unpackUnsignedShortInteger();
		}

		$stream->unpackUnsignedShortInteger(); // Reserved pad.

		for ($i = 0; $i < $segCount; $i++) {
			$format['startCode'][] = $stream->unpackUnsignedShortInteger();
		}

		for ($i = 0; $i < $segCount; $i++) {
			$format['idDelta'][] = $stream->unpackUnsignedShortInteger();
		}

		$idRangeOffsetsStart = $stream->getOffset();

		for ($i = 0; $i < $segCount; $i++) {
			$format['idRangeOffset'][] = $stream->unpackUnsignedShortInteger();
		}

		for ($i = 0; $i < $segCount - 1; $i++) {
			$glyphIndex = 0;
			$endCode = $format['endCode'][$i];
			$startCode = $format['startCode'][$i];
			$idDelta = $format['idDelta'][$i];
			$idRangeOffset = $format['idRangeOffset'][$i];

			for ($c = $startCode; $c < $endCode; $c++) {
				if ($idRangeOffset !== 0) {
					$startCodeOffset = ($c - $startCode) * 2;
					$currentRangeOffset = $i * 2; // 2 because the numbers are 2 bytes big.

					$glyphIndexOffset =
						$idRangeOffsetsStart + // where all offsets started
						$currentRangeOffset + // offset for the current range
						$idRangeOffset + // offset between the id range table and the glyphIdArray[]
						$startCodeOffset; // gets us finally to the character

					$stream->setOffset($glyphIndexOffset);
					$glyphIndex = $stream->unpackUnsignedShortInteger();
					if ($glyphIndex !== 0) {
						// & 0xffff is modulo 65536.
						$glyphIndex = ($glyphIndex + $idDelta) & 0xffff;
					}
				} else {
					$glyphIndex = ($c + $idDelta) & 0xffff;
				}
				$format['glyphIndexMap'][$c] = $glyphIndex;
			}
		}

		return $format;
	}

	/**
	 * Sets the version of the name table.
	 *
	 * @param int $version The version number.
	 * @return TrueTypeFontCharacterToGlyphIndexMappingTable
	 * @throws InvalidArgumentException If the version is not valid.
	 */
	public function setVersion(int $version): TrueTypeFontCharacterToGlyphIndexMappingTable
	{
		if (!IntegerValidator::isValid($version)) {
			throw new InvalidArgumentException("Version is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->version = $version;
		return $this;
	}

	/**
	 * Gets the version of the name table.
	 *
	 * @return int
	 */
	public function getVersion(): int
	{
		return $this->version;
	}

	/**
	 * Sets the count of the records.
	 *
	 * @param int $count The number of records.
	 * @return TrueTypeFontCharacterToGlyphIndexMappingTable
	 * @throws InvalidArgumentException If the version is not valid.
	 */
	public function setCount(int $count): TrueTypeFontCharacterToGlyphIndexMappingTable
	{
		if (!IntegerValidator::isValid($count)) {
			throw new InvalidArgumentException("Count is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->count = $count;
		return $this;
	}

	/**
	 * Gets the count of records.
	 *
	 * @return int
	 */
	public function getCount(): int
	{
		return $this->count;
	}

	/**
	 * Sets the storage offset of the name table.
	 *
	 * @param int $storageOffset The storage offset.
	 * @return TrueTypeFontCharacterToGlyphIndexMappingTable
	 * @throws InvalidArgumentException If the version is not valid.
	 */
	public function setStorageOffset(int $storageOffset): TrueTypeFontCharacterToGlyphIndexMappingTable
	{
		if (!IntegerValidator::isValid($storageOffset)) {
			throw new InvalidArgumentException("Storage offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}
		$this->storageOffset = $storageOffset;
		return $this;
	}

	/**
	 * Gets the version of the name table.
	 *
	 * @return int
	 */
	public function getStorageOffset(): int
	{
		return $this->storageOffset;
	}
}