<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Font\TrueType\TrueTypeFontHeadTable;
use Papier\Font\TrueType\TrueTypeFontHorizontalHeaderTable;
use Papier\Font\TrueType\TrueTypeFontNameTable;
use Papier\Font\TrueType\TrueTypeFontOS2Table;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\StreamType;
use Papier\Validator\EncodingValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class TrueTypeFontDictionaryType extends FontDictionaryType
{

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
	public function setEncoding($encoding): TrueTypeFontDictionaryType
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
		$stream = TrueTypeFontFileHelper::getInstance()->open($pathToFontFile);

		$fontBBoxStream = Factory::create('Papier\Type\Base\StreamType', null, true);
		$fontBBoxStream->setContent(file_get_contents($pathToFontFile));

		$fd = $this->getFontDescriptor();

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

		// Head table
		if (isset($tables['head'])) {
			$table = new TrueTypeFontHeadTable();
			$table->setHelper($stream);
			$table->setOffset($tables['head']['offset']);
			$table->parse();

			$fontBBox = [$table->getXMin(), $table->getYMin(), $table->getXMax(), $table->getYMax()];
			$fd->setFontBBox($fontBBox);
		}

		// Horizontal header table
		if (isset($tables['hhea'])) {
			$table = new TrueTypeFontHorizontalHeaderTable();
			$table->setHelper($stream);
			$table->setOffset($tables['hhea']['offset']);
			$table->parse();

			$fd->setAscent($table->getAscent());
			$fd->setDescent($table->getDescent());
		}

		// OS/2 table
		if (isset($tables['OS/2'])) {
			$table = new TrueTypeFontOS2Table();
			$table->setHelper($stream);
			$table->setOffset($tables['OS/2']['offset']);
			$table->parse();

			$fd->setCapHeight($table->getSCapHeight());
		}

		// Name table
		if (isset($tables['name'])) {
			$table = new TrueTypeFontNameTable();
			$table->setHelper($stream);
			$table->setOffset($tables['name']['offset']);
			$table->parse();

			$name = $table->getPostscriptName();
			if (!is_null($name)) {
				$fd->setFontName($name);
				$this->setBaseFont($name);
			}
		}

		$fd->setItalicAngle(0);
		$fd->setStemV(80);
		$fd->setFlags(0);

		$fd->setFontFile2($fontBBoxStream);

		$this->setFontDescriptor($fd);

		$stream->close();

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