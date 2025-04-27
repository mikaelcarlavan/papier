<?php

namespace Papier\Helpers;

use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Font\TrueType\TrueTypeFontCharacterToGlyphIndexMappingTable;
use Papier\Font\TrueType\TrueTypeFontGlyphDataTable;
use Papier\Font\TrueType\TrueTypeFontHeadTable;
use Papier\Font\TrueType\TrueTypeFontHorizontalHeaderTable;
use Papier\Font\TrueType\TrueTypeFontHorizontalMetricsTable;
use Papier\Font\TrueType\TrueTypeFontIndexToLocationTable;
use Papier\Font\TrueType\TrueTypeFontMaximumProfileTable;
use Papier\Font\TrueType\TrueTypeFontNameTable;
use Papier\Font\TrueType\TrueTypeFontOS2Table;
use Papier\Type\TrueTypeFontDictionaryType;

class TrueTypeFontFileHelper extends FileHelper
{
	/**
	 * Instance of the object.
	 *
	 * @var ?TrueTypeFontFileHelper
	 */
	private static ?TrueTypeFontFileHelper $instance = null;

	/**
	 * Font's tables.
	 *
	 * @var array
	 */
	private array $tables = [];

	/**
	 * Get tables
	 *
	 * @return array
	 */
	public function getTables(): array
	{
		return $this->tables;
	}

	/**
	 * Set tables
	 *
	 * @param array $tables
	 * @return TrueTypeFontFileHelper
	 */
	public function setTables(array $tables): TrueTypeFontFileHelper
	{
		$this->tables = $tables;
		return $this;
	}

	/**
	 * Get table
	 *
	 * @param string $tag
	 * @return ?TrueTypeFontTable
	 */
	public function getTable(string $tag): ?TrueTypeFontTable
	{
		$tables = $this->getTables();
		return $tables[$tag] ?? null;
	}

	/**
	 * Get instance of helper.
	 *
	 * @return TrueTypeFontFileHelper
	 */
	public static function getInstance(): TrueTypeFontFileHelper
	{
		if (is_null(self::$instance)) {
			self::$instance = new TrueTypeFontFileHelper();
		}

		return self::$instance;
	}

	/**
	 * Unpack 16.16-bit signed fixed-point number from stream
	 *
	 * @return float
	 */
	public function unpackFixed(): float
	{
		$integerPart = $this->unpackShortInteger();
		$realPart = $this->unpackShortInteger();
		return $integerPart + $realPart / (2 ** 16);
	}

	/**
	 * Unpack date from stream
	 *
	 * @return \DateTime
	 */
	public function unpackDate(): \DateTime
	{
		$date = new \DateTime();
		$date->setDate(1904, 1, 1);
		$date->setTimezone(new \DateTimeZone("UTC"));

		$offset = $this->unpackUnsignedLongInteger();
		$interval = new \DateInterval(sprintf('PT%dS', $offset));
		$date->add($interval);

		return $date;
	}

	/**
	 * Unpack FWord type (16-bit integer) from stream
	 *
	 * @return int
	 */
	public function unpackFWord(): int
	{
		return $this->unpackShortInteger();
	}

	/**
	 * Unpack unsigned FWord type (16-bit unsigned integer) from stream
	 *
	 * @return int
	 */
	public function unpackUnsignedFWord(): int
	{
		return $this->unpackUnsignedShortInteger();
	}

	/**
	 * Unpack F2DOT14 type from stream
	 *
	 * @return float
	 */
	public function unpackF2Dot14(): float
	{
		$value = $this->unpackInteger();
		return $value / (2 ** 14);
	}

	/**
	 * Unpack Offset 16 type from stream
	 *
	 * @return int
	 */
	public function unpackOffset16(): int
	{
		return $this->unpackUnsignedShortInteger();
	}

	/**
	 * Unpack Offset 32 type from stream
	 *
	 * @return int
	 */
	public function unpackOffset32(): int
	{
		return $this->unpackUnsignedInteger();
	}

	/**
	 * Parse font from TTF file.
	 *
	 * @param string $pathToFile
	 * @return TrueTypeFontFileHelper
	 */
	public function parse(string $pathToFile): TrueTypeFontFileHelper
	{
		$stream = $this->open($pathToFile);

		$scalerType = $stream->unpackUnsignedInteger();
		$numTables = $stream->unpackUnsignedShortInteger();
		$searchRange = $stream->unpackUnsignedShortInteger();
		$entrySelector = $stream->unpackUnsignedShortInteger();
		$rangeShift = $stream->unpackUnsignedShortInteger();

		$listOfTables = [];
		$tables = [];
		for ($i = 0; $i < $numTables; $i++) {
			$tag = trim($stream->unpackString(4));

			$listOfTables[$tag] = [
				'checksum' => $stream->unpackUnsignedInteger(),
				'offset' => $stream->unpackUnsignedInteger(),
				'length' => $stream->unpackUnsignedInteger(),
			];
		}

		// Parse tables that do not depend on others info
		$tags = [
			TrueTypeFontTable::HEAD_TABLE,
			TrueTypeFontTable::OS2_TABLE,
			TrueTypeFontTable::HORIZONTAL_HEADER_TABLE,
			TrueTypeFontTable::NAME_TABLE,
			TrueTypeFontTable::MAXIMUM_PROFILE_TABLE,
			TrueTypeFontTable::CHARACTER_TO_GLYPH_INDEX_MAPPING_TABLE
		];

		foreach ($tags as $tag) {
			if (isset($listOfTables[$tag])) {
				$table = null;
				switch ($tag) {
					case TrueTypeFontTable::HEAD_TABLE:
						$table = new TrueTypeFontHeadTable();
						break;
					case TrueTypeFontTable::OS2_TABLE:
						$table = new TrueTypeFontOS2Table();
						break;
					case TrueTypeFontTable::HORIZONTAL_HEADER_TABLE:
						$table = new TrueTypeFontHorizontalHeaderTable();
						break;
					case TrueTypeFontTable::NAME_TABLE:
						$table = new TrueTypeFontNameTable();
						break;
					case TrueTypeFontTable::MAXIMUM_PROFILE_TABLE:
						$table = new TrueTypeFontMaximumProfileTable();
						break;
					case TrueTypeFontTable::CHARACTER_TO_GLYPH_INDEX_MAPPING_TABLE:
						$table = new TrueTypeFontCharacterToGlyphIndexMappingTable();
						break;
				}

				if ($table) {
					$table->setHelper($stream);
					$table->setOffset($listOfTables[$tag]['offset']);
					$table->parse();

					$tables[$tag] = $table;
				}
			}
		}

		$numOfLongHorMetrics = 0;
		$numGlyphs = 0;
		$location = [];
		$data = [];
		$glyphIndexMap = [];

		$tag = TrueTypeFontTable::MAXIMUM_PROFILE_TABLE;
		if (isset($tables[$tag])) {
			$numGlyphs = $tables[$tag]->getNumGlyphs();
		}

		$tag = TrueTypeFontTable::HORIZONTAL_HEADER_TABLE;
		if (isset($tables[$tag])) {
			$numOfLongHorMetrics = $tables[$tag]->getNumOfLongHorMetrics();
		}

		// Horizontal metrics table
		$tag = TrueTypeFontTable::HORIZONTAL_METRICS_TABLE;
		if (isset($listOfTables[$tag])) {
			$table = new TrueTypeFontHorizontalMetricsTable();
			$table->setHelper($stream);
			$table->setOffset($listOfTables[$tag]['offset']);
			$table->setNumOfLongHorMetrics($numOfLongHorMetrics);
			$table->setNumGlyphs($numGlyphs);
			$table->parse();
			$tables[$tag] = $table;
		}

		$tag = TrueTypeFontTable::INDEX_TO_LOCATION_TABLE;
		if (isset($listOfTables[$tag])) {
			$table = new TrueTypeFontIndexToLocationTable();
			$table->setHelper($stream);
			$table->setOffset($listOfTables[$tag]['offset']);
			$table->setNumGlyphs($numGlyphs);
			$table->parse();
			$tables[$tag] = $table;

			$location = $table->getLocation();
		}

		// Index to location table
		$tag = TrueTypeFontTable::INDEX_TO_LOCATION_TABLE;
		if (isset($listOfTables[$tag])) {
			$table = new TrueTypeFontGlyphDataTable();
			$table->setHelper($stream);
			$table->setOffset($listOfTables[$tag]['offset']);
			$table->setNumGlyphs($numGlyphs);
			$table->setLocation($location);
			$table->parse();
			$tables[$tag] = $table;

			$data = $table->getData();
		}

		// Character to glyph index table
		$tag = TrueTypeFontTable::CHARACTER_TO_GLYPH_INDEX_MAPPING_TABLE;
		if (isset($tables[$tag])) {
			$glyphIndexMap = $tables[$tag]->getGlyphIndexMap();
		}

		$this->setTables($tables);
		return $this;
	}

}