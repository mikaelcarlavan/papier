<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\StreamType;
use Papier\Validator\ByteStringValidator;
use Papier\Validator\ColourComponentsValidator;
use Papier\Validator\EncodingValidator;
use Papier\Validator\FontStretchValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;
use RuntimeException;

class FontDescriptorDictionaryType extends DictionaryType
{
	/**
	 * Get font name.
	 *
	 * @return string|null
	 */
	public function getFontName(): ?string
	{
		/** @var string|null $value */
		$value = $this->getEntryValue('Name');
		return $value;
	}

	/**
	 * Set font name.
	 *
	 * @param string $name
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontName(string $name): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\NameType', $name);
		$this->setEntry('FontName', $value);
		return $this;
	}

	/**
	 * Set font family
	 *
	 * @param string $fontFamily
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontFamily(string $fontFamily): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\ByteStringType', $fontFamily);
		$this->setEntry('FontFamily', $value);
		return $this;
	}

	/**
	 * Set font stretch
	 *
	 * @param string $fontStretch
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontStretch(string $fontStretch): FontDescriptorDictionaryType
	{
		if (!FontStretchValidator::isValid($fontStretch)) {
			throw new InvalidArgumentException("Font Stretch is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\NameType', $fontStretch);
		$this->setEntry('FontStretch', $value);
		return $this;
	}

	/**
	 * Set font weight
	 *
	 * @param string $fontWeight
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontWeight(string $fontWeight): FontDescriptorDictionaryType
	{
		if (!FontStretchValidator::isValid($fontWeight)) {
			throw new InvalidArgumentException("Font Weight is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\NumberType', $fontWeight);
		$this->setEntry('FontWeight', $value);
		return $this;
	}

	/**
	 * Set flags.
	 *
	 * @param int $flags
	 * @return FontDescriptorDictionaryType
	 */
	public function setFlags(int $flags): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\IntegerType', $flags);
		$this->setEntry('Flags', $value);
		return $this;
	}

	/**
	 * Set font bounding box.
	 *
	 * @param array<mixed> $fontBBox
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontBBox(array $fontBBox): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\RectangleNumbersArrayType', $fontBBox);
		$this->setEntry('FontBBox', $value);
		return $this;
	}

	/**
	 * Set italic angle.
	 *
	 * @param float $italicAngle
	 * @return FontDescriptorDictionaryType
	 */
	public function setItalicAngle(float $italicAngle): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $italicAngle);
		$this->setEntry('ItalicAngle', $value);
		return $this;
	}

	/**
	 * Set ascent.
	 *
	 * @param float $ascent
	 * @return FontDescriptorDictionaryType
	 */
	public function setAscent(float $ascent): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $ascent);
		$this->setEntry('Ascent', $value);
		return $this;
	}

	/**
	 * Set descent.
	 *
	 * @param float $descent
	 * @return FontDescriptorDictionaryType
	 */
	public function setDescent(float $descent): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $descent);
		$this->setEntry('Ascent', $value);
		return $this;
	}

	/**
	 * Set leading.
	 *
	 * @param float $leading
	 * @return FontDescriptorDictionaryType
	 */
	public function setLeading(float $leading): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $leading);
		$this->setEntry('Leading', $value);
		return $this;
	}

	/**
	 * Set cap height.
	 *
	 * @param float $capHeight
	 * @return FontDescriptorDictionaryType
	 */
	public function setCapHeight(float $capHeight): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $capHeight);
		$this->setEntry('CapHeight', $value);
		return $this;
	}

	/**
	 * Set X-height.
	 *
	 * @param float $xHeight
	 * @return FontDescriptorDictionaryType
	 */
	public function setXHeight(float $xHeight): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $xHeight);
		$this->setEntry('XHeight', $value);
		return $this;
	}

	/**
	 * Set vertical stem.
	 *
	 * @param float $stemV
	 * @return FontDescriptorDictionaryType
	 */
	public function setStemV(float $stemV): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $stemV);
		$this->setEntry('StemV', $value);
		return $this;
	}

	/**
	 * Set horizontal stem.
	 *
	 * @param float $stemH
	 * @return FontDescriptorDictionaryType
	 */
	public function setStemH(float $stemH): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $stemH);
		$this->setEntry('StemH', $value);
		return $this;
	}

	/**
	 * Set average width.
	 *
	 * @param float $avgWidth
	 * @return FontDescriptorDictionaryType
	 */
	public function setAvgWidth(float $avgWidth): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $avgWidth);
		$this->setEntry('AvgWidth', $value);
		return $this;
	}

	/**
	 * Set maximum width.
	 *
	 * @param float $maxWidth
	 * @return FontDescriptorDictionaryType
	 */
	public function setMaxWidth(float $maxWidth): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $maxWidth);
		$this->setEntry('MaxWidth', $value);
		return $this;
	}

	/**
	 * Set missing width.
	 *
	 * @param float $missingWidth
	 * @return FontDescriptorDictionaryType
	 */
	public function setMissingWidth(float $missingWidth): FontDescriptorDictionaryType
	{
		$value = Factory::create('Papier\Type\NumberType', $missingWidth);
		$this->setEntry('StemH', $value);
		return $this;
	}

	/**
	 * Set stream containing a Type 1 font program.
	 *
	 * @param StreamType $stream
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontFile(StreamType $stream): FontDescriptorDictionaryType
	{
		$this->setEntry('FontFile', $stream);
		return $this;
	}

	/**
	 * Set stream containing a TrueType font program.
	 *
	 * @param StreamType $stream
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontFile2(StreamType $stream): FontDescriptorDictionaryType
	{
		$this->setEntry('FontFile2', $stream);
		return $this;
	}

	/**
	 * Set stream containing a font program.
	 *
	 * @param StreamType $stream
	 * @return FontDescriptorDictionaryType
	 */
	public function setFontFile3(StreamType $stream): FontDescriptorDictionaryType
	{
		$this->setEntry('FontFile3', $stream);
		return $this;
	}


	/**
	 * Set character set.
	 *
	 * @param string $charSet
	 * @return FontDescriptorDictionaryType
	 */
	public function setCharSet(string $charSet): FontDescriptorDictionaryType
	{

		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'FontDescriptor');
		$this->setEntry('Type', $type);


		if (!$this->hasEntry('FontName')) {
			throw new RuntimeException("FontName is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('Flags')) {
			throw new RuntimeException("Flags is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('ItalicAngle')) {
			throw new RuntimeException("ItalicAngle is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		return parent::format();
	}
}