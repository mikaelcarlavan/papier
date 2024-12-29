<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Type\Base\IntegerType;
use Papier\Type\Base\NameType;
use Papier\Type\Base\StreamType;
use Papier\Validator\EncodingValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class Type1FontType extends FontType
{
    /**
     * Set basefont (PostScript) name.
     *  
     * @param string $name
     * @return Type1FontType
     */
    public function setBaseFont(string $name): Type1FontType
    {
        $value = Factory::create('Papier\Type\Base\NameType', $name);
		$this->setEntry('BaseFont', $value);
        return $this;
    }

    /**
     * Set first character code.
     *  
     * @param int $fc
     * @return Type1FontType
     */
    public function setFirstChar(int $fc): Type1FontType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $fc);
		$this->setEntry('FirstChar', $value);
        return $this;
    }

    /**
     * Set last character code.
     *  
     * @param int $lc
     * @return Type1FontType
     */
    public function setLastChar(int $lc): Type1FontType
    {
        $value = Factory::create('Papier\Type\Base\IntegerType', $lc);
		$this->setEntry('LastChar', $value);
        return $this;
    }

    /**
     * Set widths.
     *  
     * @param ArrayType $widths
     * @return Type1FontType
     */
    public function setWidths(ArrayType $widths): Type1FontType
    {
		$this->setEntry('Widths', $widths);
        return $this;
    }

    /**
     * Set font descriptor.
     *  
     * @param  DictionaryType  $fd
     * @return Type1FontType
     */
    public function setFontDescriptor(DictionaryType $fd): Type1FontType
    {
		$this->setEntry('FontDescriptor', $fd);
        return $this;
    }

    /**
     * Set encoding.
     *  
     * @param  mixed  $encoding
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType' or 'string'.
     * @return Type1FontType
     */
    public function setEncoding($encoding): Type1FontType
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
     * @return Type1FontType
     */
    public function setToUnicode(StreamType $toUnicode): Type1FontType
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

        return parent::format();
    }
}