<?php

namespace Papier\Helpers;

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

		$stream = TrueTypeFontFileHelper::getInstance()->open($pathToFile);

		$scalerType = $stream->unpackUnsignedInteger();
		$numTables = $stream->unpackUnsignedShortInteger();
		$searchRange = $stream->unpackUnsignedShortInteger();
		$entrySelector = $stream->unpackUnsignedShortInteger();
		$rangeShift = $stream->unpackUnsignedShortInteger();

		$tables = [];
		for ($i = 0; $i < $numTables; $i++) {
			$tag = trim($stream->unpackString(4));

			$tables[$tag] = [
				'checksum' => $stream->unpackUnsignedInteger(),
				'offset' => $stream->unpackUnsignedInteger(),
				'length' => $stream->unpackUnsignedInteger(),
			];
		}


		return $this;
	}

}