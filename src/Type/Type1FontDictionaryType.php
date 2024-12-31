<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;
use Papier\Text\Encoding;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\IntegerType;
use Papier\Type\Base\NameType;
use Papier\Type\Base\StreamType;
use Papier\Validator\EncodingValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class Type1FontDictionaryType extends FontDictionaryType
{
    /**
     * Set basefont (PostScript) name.
     *  
     * @param string $name
     * @return Type1FontDictionaryType
     */
    public function setBaseFont(string $name): Type1FontDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\NameType', $name);
		$this->setEntry('BaseFont', $value);
        return $this;
    }

    /**
     * Set first character code.
     *  
     * @param int $fc
     * @return Type1FontDictionaryType
     */
    public function setFirstChar(int $fc): Type1FontDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $fc);
		$this->setEntry('FirstChar', $value);
        return $this;
    }

    /**
     * Set last character code.
     *  
     * @param int $lc
     * @return Type1FontDictionaryType
     */
    public function setLastChar(int $lc): Type1FontDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $lc);
		$this->setEntry('LastChar', $value);
        return $this;
    }

    /**
     * Set widths.
     *  
     * @param ArrayType $widths
     * @return Type1FontDictionaryType
     */
    public function setWidths(ArrayType $widths): Type1FontDictionaryType
    {
		$this->setEntry('Widths', $widths);
        return $this;
    }

    /**
     * Set font descriptor.
     *  
     * @param  DictionaryType  $fd
     * @return Type1FontDictionaryType
     */
    public function setFontDescriptor(DictionaryType $fd): Type1FontDictionaryType
    {
		$this->setEntry('FontDescriptor', $fd);
        return $this;
    }

    /**
     * Set encoding.
     *  
     * @param  mixed  $encoding
     * @return Type1FontDictionaryType
     *@throws InvalidArgumentException if the provided argument is not of type 'DictionaryType' or 'string'.
     */
    public function setEncoding($encoding): Type1FontDictionaryType
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
     * @return Type1FontDictionaryType
	 */
    public function setToUnicode(StreamType $toUnicode): Type1FontDictionaryType
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

        $subtype = Factory::create('Papier\Type\Base\NameType', 'Type1');
        $this->setEntry('Subtype', $subtype);

        if (!$this->hasEntry('Name')) {
            throw new RuntimeException("Name is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('BaseFont')) {
            throw new RuntimeException("BaseFont is missing. See ".__CLASS__." class's documentation for possible values.");
        }

		if ($this->hasEntry('Encoding')) {
			if ($this->getEntryValue('Encoding') == Encoding::MAC_EXPERT) {
				throw new RuntimeException("Encoding is incompatible with Type 1 fonts. See ".__CLASS__." class's documentation for possible values.");
			}
		}

        return parent::format();
    }
}