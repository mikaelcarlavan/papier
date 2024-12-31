<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\DictionaryObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\IntegerType;
use Papier\Type\Base\NameType;
use Papier\Type\Base\StreamType;
use Papier\Validator\EncodingValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;
use RuntimeException;

class Type3FontDictionaryType extends FontDictionaryType
{
	/**
	 * Set font bounding box.
	 *
	 * @param RectangleNumbersArrayType $fontBBox
	 * @return Type3FontDictionaryType
	 */
	public function setFontBBox(RectangleNumbersArrayType $fontBBox): Type3FontDictionaryType
	{
		$value = Factory::create('Papier\Type\RectangleNumbersArrayType', $fontBBox);
		$this->setEntry('FontBBox', $value);
		return $this;
	}

	/**
	 * Set font matrix.
	 *
	 * @param array<float> $fontMatrix
	 * @return Type3FontDictionaryType
	 */
	public function setFontMatrix(array $fontMatrix): Type3FontDictionaryType
	{
		if (!NumbersArrayValidator::isValid($fontMatrix, 6)) {
			throw new InvalidArgumentException("Font Matrix is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $fontMatrix);
		$this->setEntry('FontMatrix', $value);
		return $this;
	}

	/**
	 * Set char procs.
	 *
	 * @param DictionaryType $charProcs
	 * @return Type3FontDictionaryType
	 */
	public function setCharProcs(DictionaryType $charProcs): Type3FontDictionaryType
	{
		$this->setEntry('CharProcs', $charProcs);
		return $this;
	}


	/**
	 * Set first character code.
	 *
	 * @param int $fc
	 * @return Type3FontDictionaryType
	 */
	public function setFirstChar(int $fc): Type3FontDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\IntegerType', $fc);
		$this->setEntry('FirstChar', $value);
		return $this;
	}

	/**
	 * Set last character code.
	 *
	 * @param int $lc
	 * @return Type3FontDictionaryType
	 */
	public function setLastChar(int $lc): Type3FontDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\IntegerType', $lc);
		$this->setEntry('LastChar', $value);
		return $this;
	}

	/**
	 * Set widths.
	 *
	 * @param ArrayType $widths
	 * @return Type3FontDictionaryType
	 */
	public function setWidths(ArrayType $widths): Type3FontDictionaryType
	{
		$this->setEntry('Widths', $widths);
		return $this;
	}

	/**
	 * Set font descriptor.
	 *
	 * @param  DictionaryType  $fd
	 * @return Type3FontDictionaryType
	 */
	public function setFontDescriptor(DictionaryType $fd): Type3FontDictionaryType
	{
		$this->setEntry('FontDescriptor', $fd);
		return $this;
	}

	/**
	 * Set encoding.
	 *
	 * @param  mixed  $encoding
	 * @return Type3FontDictionaryType
	 *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryType' or 'string'.
	 */
	public function setEncoding($encoding): Type3FontDictionaryType
	{
		if (!$encoding instanceof DictionaryType && !StringValidator::isValid($encoding)) {
			throw new InvalidArgumentException("Encoding is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		if (StringValidator::isValid($encoding)) {
			/** @var string $encoding */
			if (!EncodingValidator::isValid($encoding)) {
				throw new InvalidArgumentException("Encoding is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}
		}

		$value = $encoding instanceof DictionaryType ? $encoding : Factory::create('Papier\Type\Base\NameType', $encoding);

		$this->setEntry('Encoding', $value);
		return $this;
	}

	/**
	 * Set resources.
	 *
	 * @param DictionaryType $resources
	 * @return Type3FontDictionaryType
	 */
	public function setResources(DictionaryType $resources): Type3FontDictionaryType
	{
		$this->setEntry('Resources', $resources);
		return $this;
	}
	
	/**
	 * Set map to unicode values.
	 *
	 * @param  StreamType  $toUnicode
	 * @return Type3FontDictionaryType
	 */
	public function setToUnicode(StreamType $toUnicode): Type3FontDictionaryType
	{
		$this->setEntry('ToUnicode', $toUnicode);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Font');
		$this->setEntry('Type', $type);

		$subtype = Factory::create('Papier\Type\Base\NameType', 'Type3');
		$this->setEntry('Subtype', $subtype);

		if (!$this->hasEntry('Name')) {
			throw new RuntimeException("Name is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('FontBBox')) {
			throw new RuntimeException("FontBBox is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('FontMatrix')) {
			throw new RuntimeException("FontMatrix is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('CharProcs')) {
			throw new RuntimeException("CharProcs is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('Encoding')) {
			throw new RuntimeException("Encoding is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('FirstChar')) {
			throw new RuntimeException("FirstChar is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('LastChar')) {
			throw new RuntimeException("LastChar is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('Widths')) {
			throw new RuntimeException("Widths is missing. See ".__CLASS__." class's documentation for possible values.");
		} else {
			/** @var ArrayType $widths */
			$widths = $this->getEntry('Widths');

			/** @var int $firstChar */
			$firstChar = $this->getEntryValue('FirstChar');
			/** @var int $lastChar */
			$lastChar = $this->getEntryValue('LastChar');

			if (count($widths) != ($lastChar - $firstChar + 1)) {
				throw new RuntimeException("Widths size is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}
		}

		return parent::format();
	}
}