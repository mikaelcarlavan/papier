<?php

namespace Papier\Font\TrueType\Base;

use InvalidArgumentException;
use Papier\Helpers\FileHelper;
use Papier\Validator\IntegerValidator;

class TrueTypeFontTable
{
	/**
	 * Offset.
	 *
	 * @var int
	 */
	protected int $offset = 0;

	/**
	 * Length.
	 *
	 * @var int
	 */
	protected int $length = 0;

	/**
	 * Checksum.
	 *
	 * @var int
	 */
	protected int $checksum = 0;

	/**
	 * Helper.
	 *
	 * @var FileHelper $helper
	 */
	protected FileHelper $helper;

	/**
	 * Set table's offset
	 *
	 * @param int $offset
	 * @return self
	 */
	public function setOffset(int $offset = 0): self
	{
		if (!IntegerValidator::isValid($offset) || $offset < 0) {
			throw new InvalidArgumentException("Offset is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->offset = $offset;

		return $this;
	}

	/**
	 * Get table's offset
	 *
	 * @return int
	 */
	public function getOffset(): int
	{
		return $this->offset;
	}

	/**
	 * Set file helper.
	 *
	 * @param FileHelper $helper
	 * @return self
	 */
	public function setHelper(FileHelper $helper): self
	{
		$this->helper = $helper;

		return $this;
	}

	/**
	 * Get filer helper.
	 *
	 * @return FileHelper
	 */
	public function getHelper(): FileHelper
	{
		return $this->helper;
	}

	/**
	 * Set table's length
	 *
	 * @param int $length
	 * @return self
	 */
	public function setLength(int $length = 0): self
	{
		if (!IntegerValidator::isValid($length) || $length < 0) {
			throw new InvalidArgumentException("Length is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->length = $length;

		return $this;
	}

	/**
	 * Get table's length.
	 *
	 * @return int
	 */
	public function getLength(): int
	{
		return $this->length;
	}

	/**
	 * Set table's checksum.
	 *
	 * @param int $checksum
	 * @return self
	 */
	public function setChecksum(int $checksum = 0): self
	{
		if (!IntegerValidator::isValid($checksum) || $checksum < 0) {
			throw new InvalidArgumentException("Checksum is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->checksum = $checksum;

		return $this;
	}

	/**
	 * Get table's checksum.
	 *
	 * @return int
	 */
	public function getChecksum(): int
	{
		return $this->checksum;
	}

	/**
	 * Extract table's data
	 *
	 */
	public function parse(): void
	{

	}
}