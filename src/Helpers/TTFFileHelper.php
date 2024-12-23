<?php

namespace Papier\Helpers;

class TTFFileHelper extends FileHelper
{
	/**
	 * Get instance of helper.
	 *
	 * @return TTFFileHelper
	 */
	public static function getInstance(): TTFFileHelper
	{
		if (is_null(self::$instance)) {
			self::$instance = new TTFFileHelper();
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
		$value = $integerPart + $realPart / (2 ** 16);

		var_dump($integerPart, $realPart);
		return $value;
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
	 * Unpack FWord type (16-bit unsigned integer) from stream
	 *
	 * @return int
	 */
	public function unpackFWord(): int
	{
		return $this->unpackUnsignedShortInteger();
	}
}