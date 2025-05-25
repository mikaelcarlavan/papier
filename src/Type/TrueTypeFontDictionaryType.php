<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Component\SegmentComponent;
use Papier\Factory\Factory;
use Papier\Font\FontDescriptorFlag;
use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Font\TrueType\TrueTypeFontCharacterToGlyphIndexMappingTable;
use Papier\Font\TrueType\TrueTypeFontHeadTable;
use Papier\Font\TrueType\TrueTypeFontHorizontalHeaderTable;
use Papier\Font\TrueType\TrueTypeFontHorizontalMetricsTable;
use Papier\Font\TrueType\TrueTypeFontKerningTable;
use Papier\Font\TrueType\TrueTypeFontNameTable;
use Papier\Font\TrueType\TrueTypeFontOS2Table;
use Papier\Font\TrueType\TrueTypeFontPostTable;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\IntegerType;
use Papier\Type\Base\StreamType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\EncodingValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RealValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class TrueTypeFontDictionaryType extends FontDictionaryType
{
	/**
	 * Default advance width
	 *
	 * @var int
	 */
	protected int $defaultAdvanceWidth = 0;

	/**
	 * Font char codes.
	 *
	 * @var array
	 */
	protected array $charCodes = [];

	/**
	 * Kerning pairs.
	 *
	 * @var array
	 */
	protected array $kerningPairs = [];

	/**
	 * The line gap in font units.
	 *
	 * @var int
	 */
	protected int $lineGap = 0;

	/**
	 * Scale factor.
	 *
	 * @var float
	 */
	protected int $scaleFactor;

	/**
	 * Get units per EM.
	 *
	 * @return float
	 */
	public function getScaleFactor(): float
	{
		return $this->scaleFactor;
	}

	/**
	 * Set scale factor.
	 *
	 * @param float $scaleFactor
	 * @return TrueTypeFontDictionaryType
	 */
	public function setScaleFactor(float $scaleFactor): TrueTypeFontDictionaryType
	{
		if (!RealValidator::isValid($scaleFactor)) {
			throw new InvalidArgumentException("Scale factor is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->scaleFactor = $scaleFactor;

		return $this;
	}

	/**
	 * Sets the line gap in font units.
	 *
	 * @param int $lineGap The line gap value.
	 * @return TrueTypeFontDictionaryType
	 * @throws InvalidArgumentException if the value is not valid.
	 */
	public function setLineGap(int $lineGap): TrueTypeFontDictionaryType
	{
		if (!IntegerValidator::isValid($lineGap)) {
			throw new InvalidArgumentException("Line Gap is not valid. See " . __CLASS__ . " class's documentation for possible values.");
		}
		$this->lineGap = $lineGap;
		return $this;
	}

	/**
	 * Gets the line gap in font units.
	 *
	 * @return int
	 */
	public function getLineGap(): int
	{
		return $this->lineGap;
	}

	/**
	 * Set kerning pairs.
	 *
	 * @param array $kerningPairs
	 * @return TrueTypeFontDictionaryType
	 */
	public function setKerningPairs(array $kerningPairs): TrueTypeFontDictionaryType
	{
		if (!ArrayValidator::isValid($kerningPairs)) {
			throw new InvalidArgumentException("Kerning pairs is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->kerningPairs = $kerningPairs;

		return $this;
	}

	/**
	 * Get kerning pairs.
	 *
	 * @return array
	 */
	public function getKerningPairs(): array
	{
		return $this->kerningPairs;
	}

	/**
	 * Set basefont (PostScript) name.
	 *
	 * @param string $name
	 * @return TrueTypeFontDictionaryType
	 */
	public function setBaseFont(string $name): TrueTypeFontDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\NameType', $name);
		$this->setEntry('BaseFont', $value);
		return $this;
	}

	/**
	 * Set first character code.
	 *
	 * @param int $fc
	 * @return TrueTypeFontDictionaryType
	 */
	public function setFirstChar(int $fc): TrueTypeFontDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\IntegerType', $fc);
		$this->setEntry('FirstChar', $value);
		return $this;
	}

	/**
	 * Get first character code.
	 *
	 * @return IntegerType
	 */
	public function getFirstChar(): IntegerType
	{
		/** @var IntegerType $firstChar */
		$firstChar = $this->getEntry('FirstChar');
		return $firstChar;
	}

	/**
	 * Set char codes.
	 *
	 * @param array $charCodes
	 * @return TrueTypeFontDictionaryType
	 */
	public function setCharCodes(array $charCodes): TrueTypeFontDictionaryType
	{
		$this->charCodes = $charCodes;
		return $this;
	}

	/**
	 * Get font char codes
	 *
	 * @return array
	 */
	public function getCharCodes(): array
	{
		return $this->charCodes;
	}

	/**
	 * Set default advance width.
	 *
	 * @param int $defaultAdvanceWidth
	 * @return TrueTypeFontDictionaryType
	 */
	public function setDefaultAdvanceWidth(int $defaultAdvanceWidth): TrueTypeFontDictionaryType
	{
		$this->defaultAdvanceWidth = $defaultAdvanceWidth;
		return $this;
	}

	/**
	 * Get default advance width.
	 *
	 * @return int
	 */
	public function getDefaultAdvanceWidth(): int
	{
		return $this->defaultAdvanceWidth;
	}

	/**
	 * Set last character code.
	 *
	 * @param int $lc
	 * @return TrueTypeFontDictionaryType
	 */
	public function setLastChar(int $lc): TrueTypeFontDictionaryType
	{
		$value = Factory::create('Papier\Type\Base\IntegerType', $lc);
		$this->setEntry('LastChar', $value);
		return $this;
	}

	/**
	 * Get last character code.
	 *
	 * @return IntegerType
	 */
	public function getLastChar(): IntegerType
	{
		/** @var IntegerType $lastChar */
		$lastChar = $this->getEntry('LastChar');
		return $lastChar;
	}

	/**
	 * Set widths.
	 *
	 * @param ArrayType $widths
	 * @return TrueTypeFontDictionaryType
	 */
	public function setWidths(ArrayType $widths): TrueTypeFontDictionaryType
	{
		$this->setEntry('Widths', $widths);
		return $this;
	}

	/**
	 * Get widths.
	 *
	 * @return ArrayType
	 */
	public function getWidths(): ArrayType
	{
		/** @var ArrayType $widths */
		$widths = $this->getEntry('Widths');
		return $widths;
	}

	/**
	 * Set font descriptor.
	 *
	 * @param  FontDescriptorDictionaryType  $fd
	 * @return TrueTypeFontDictionaryType
	 */
	public function setFontDescriptor(FontDescriptorDictionaryType $fd): TrueTypeFontDictionaryType
	{
		$this->setEntry('FontDescriptor', $fd);
		return $this;
	}

	/**
	 * Get font description.
	 *
	 * @return FontDescriptorDictionaryType
	 */
	public function getFontDescriptor(): FontDescriptorDictionaryType
	{
		if (!$this->hasEntry('FontDescriptor')) {
			$value = Factory::create('Papier\Type\FontDescriptorDictionaryType', null, true);
			$this->setEntry('FontDescriptor', $value);
		}

		/** @var FontDescriptorDictionaryType $fd */
		$fd = $this->getEntry('FontDescriptor');
		return $fd;
	}

	/**
	 * Set encoding.
	 *
	 * @param  mixed  $encoding
	 * @return TrueTypeFontDictionaryType
	 *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryType' or 'string'.
	 */
	public function setEncoding(mixed $encoding): TrueTypeFontDictionaryType
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
	 * Set map to unicode values.
	 *
	 * @param  StreamType  $toUnicode
	 * @return TrueTypeFontDictionaryType
	 */
	public function setToUnicode(StreamType $toUnicode): TrueTypeFontDictionaryType
	{
		$this->setEntry('ToUnicode', $toUnicode);
		return $this;
	}

	/**
	 * Parse font from TTF file.
	 *
	 * @param  string  $pathToFontFile
	 * @return TrueTypeFontDictionaryType
	 */
	public function load(string $pathToFontFile): TrueTypeFontDictionaryType
	{
		$scaleFactor = 1;
		$helper = TrueTypeFontFileHelper::getInstance()->parse($pathToFontFile);

		$fontBBoxStream = Factory::create('Papier\Type\Base\StreamType', null, true);
		$fontBBoxStream->setContent(file_get_contents($pathToFontFile));

		$fontDescriptor = $this->getFontDescriptor();

		/** @var ?TrueTypeFontHeadTable $head */
		$head = $helper->getTable(TrueTypeFontTable::HEAD_TABLE);

		if ($head) {
			$scaleFactor = 1000 / $head->getUnitsPerEm();
			$fontBBox = [
				$scaleFactor * $head->getXMin(),
				$scaleFactor * $head->getYMin(),
				$scaleFactor * $head->getXMax(),
				$scaleFactor * $head->getYMax()
			];
			$fontDescriptor->setFontBBox($fontBBox);

		}

		/** @var ?TrueTypeFontHorizontalHeaderTable $horizontalHeader */
		$horizontalHeader = $helper->getTable(TrueTypeFontTable::HORIZONTAL_HEADER_TABLE);

		if ($horizontalHeader) {
			$fontDescriptor->setAscent($scaleFactor * $horizontalHeader->getAscent());
			$fontDescriptor->setDescent($scaleFactor * $horizontalHeader->getDescent());
			$this->setLineGap($scaleFactor * $horizontalHeader->getLineGap());
		}

		/** @var ?TrueTypeFontOS2Table $os2 */
		$os2 = $helper->getTable(TrueTypeFontTable::OS2_TABLE);
		if ($os2) {
			$capHeight = $os2->getSCapHeight() ?? ($horizontalHeader ? $horizontalHeader->getAscent() : 0);
			$fontDescriptor->setCapHeight($scaleFactor * $capHeight);
		}

		/** @var ?TrueTypeFontNameTable $name */
		$name = $helper->getTable(TrueTypeFontTable::NAME_TABLE);
		if ($name) {
			$postscriptName = $name->getPostscriptName();
			if (!is_null($postscriptName)) {
				$fontDescriptor->setFontName($postscriptName);
				$this->setBaseFont($postscriptName);
			}
		}

		/** @var ?TrueTypeFontCharacterToGlyphIndexMappingTable $cmap */
		$cmap = $helper->getTable(TrueTypeFontTable::CHARACTER_TO_GLYPH_INDEX_MAPPING_TABLE);

		/** @var ?TrueTypeFontHorizontalMetricsTable $horizontalMetrics */
		$horizontalMetrics = $helper->getTable(TrueTypeFontTable::HORIZONTAL_METRICS_TABLE);
		if ($horizontalMetrics && $cmap) {
			$hMetrics = $horizontalMetrics->getHMetrics();
			$glyphIndexMap = $cmap->getGlyphIndexMap();

			$widthsArray = Factory::create('Papier\Type\Base\ArrayType', null, true);
			$firstChar = min(array_keys($glyphIndexMap));
			$lastChar = max(array_keys($glyphIndexMap));

			$defaultAdvanceWidth = (int) round(end($hMetrics)['advanceWidth'] * $scaleFactor);
			$this->setDefaultAdvanceWidth($defaultAdvanceWidth);

			foreach ($glyphIndexMap as $glyphIndex) {
				if ($glyphIndex < count($hMetrics)) {
					$advanceWidth = $hMetrics[$glyphIndex]['advanceWidth'];
				} else {
					$advanceWidth = end($hMetrics)['advanceWidth'];
				}

				$advanceWidth = (int) round($advanceWidth * $scaleFactor);
				$widthsArray->append(Factory::create('Papier\Type\Base\IntegerType', $advanceWidth));
			}

			$this->setFirstChar($firstChar);
			$this->setLastChar($lastChar);
			$this->setWidths($widthsArray);
			$this->setCharCodes(array_keys($glyphIndexMap));
		}

		/** @var ?TrueTypeFontKerningTable $kerning */
		$kerning = $helper->getTable(TrueTypeFontTable::KERNING_TABLE);
		if ($kerning && $cmap) {
			$kerningPairs = [];
			$kerningIndexPairs = $kerning->getKerningPairs();
			$glyphIndexMap = $cmap->getGlyphIndexMap();
			$glyphCodeMap = [];
			foreach ($glyphIndexMap as $charCode => $glyphIndex) {
				$glyphCodeMap[$glyphIndex][] = $charCode;
			}

			foreach ($kerningIndexPairs as $leftIndex => $kerningRightIndexPairs) {
				foreach ($kerningRightIndexPairs as $rightIndex => $value) {
					foreach (($glyphCodeMap[$leftIndex] ?? []) as $leftCode) {
						foreach (($glyphCodeMap[$rightIndex] ?? []) as $rightCode) {
							$kerningPairs[$leftCode][$rightCode] = $value * $scaleFactor;
						}
					}
				}
			}

			$this->setKerningPairs($kerningPairs);
		}

		$flag = FontDescriptorFlag::NON_SYMBOLIC;
		$fontDescriptor->setItalicAngle(0);

		/** @var ?TrueTypeFontPostTable $post */
		$post = $helper->getTable(TrueTypeFontTable::POST_TABLE);
		if ($post) {
			$italicAngle = $post->getItalicAngle();
			$fontDescriptor->setItalicAngle($italicAngle);

			$isFixedPitch = $post->getIsFixedPitch();
			if ($isFixedPitch) {
				$flag |= FontDescriptorFlag::FIXED_PITCH;
			}
		}

		$fontDescriptor->setStemV(80);
		$fontDescriptor->setFlags($flag);

		$fontDescriptor->setFontFile2($fontBBoxStream);

		$this->setFontDescriptor($fontDescriptor);

		$helper->close();

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

		$subtype = Factory::create('Papier\Type\Base\NameType', 'TrueType');
		$this->setEntry('Subtype', $subtype);

		/*if (!$this->hasEntry('Name')) {
			throw new RuntimeException("Name is missing. See ".__CLASS__." class's documentation for possible values.");
		}*/

		if (!$this->hasEntry('BaseFont')) {
			throw new RuntimeException("BaseFont is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		return parent::format();
	}
}