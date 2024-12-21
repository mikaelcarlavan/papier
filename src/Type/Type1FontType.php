<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;

use Papier\Validator\StringValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\EncodingValidator;

use Papier\Factory\Factory;

use Papier\Type\DictionaryType;

use RuntimeException;
use InvalidArgumentException;

class Type1FontType extends DictionaryType
{
    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
		/** @var string|null $value */
        $value = $this->getEntryValue('Name');
        return $value;
    }

    /**
     * Set name.
     *  
     * @param string $name
     * @return Type1FontType
     */
    public function setName(string $name): Type1FontType
    {
        $value = Factory::create('Papier\Type\NameType', $name);
		$this->setEntry('Name', $value);
        return $this;
    }

    /**
     * Set basefont (PostScript) name.
     *  
     * @param string $name
     * @return Type1FontType
     */
    public function setBaseFont(string $name): Type1FontType
    {
        $value = Factory::create('Papier\Type\NameType', $name);
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
        $value = Factory::create('Papier\Type\IntegerType', $fc);
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
        $value = Factory::create('Papier\Type\IntegerType', $lc);
		$this->setEntry('LastChar', $value);
        return $this;
    }

    /**
     * Set widths.
     *  
     * @param ArrayObject $widths
     * @return Type1FontType
     */
    public function setWidths(ArrayObject $widths): Type1FontType
    {
		$this->setEntry('Widths', $widths);
        return $this;
    }

    /**
     * Set font descriptor.
     *  
     * @param  DictionaryObject  $fd
     * @return Type1FontType
     */
    public function setFontDescriptor(DictionaryObject $fd): Type1FontType
    {
		$this->setEntry('FontDescriptor', $fd);
        return $this;
    }

    /**
     * Set encoding.
     *  
     * @param  mixed  $encoding
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'string'.
     * @return Type1FontType
     */
    public function setEncoding($encoding): Type1FontType
    {
        if (!$encoding instanceof DictionaryObject && !StringValidator::isValid($encoding)) {
            throw new InvalidArgumentException("Encoding is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (StringValidator::isValid($encoding)) {
			/** @var string $encoding */
            if (!EncodingValidator::isValid($encoding)) {
                throw new InvalidArgumentException("Encoding is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        $value = $encoding instanceof DictionaryObject ? $encoding : Factory::create('Papier\Type\NameType', $encoding);

		$this->setEntry('Encoding', $value);
        return $this;
    }

    /**
     * Set map to unicde values.
     *  
     * @param  StreamObject  $tounicode
     * @return Type1FontType
     */
    public function setToUnicode(StreamObject $tounicode): Type1FontType
    {
		$this->setEntry('ToUnicode', $tounicode);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\NameType', 'Font');
        $this->setEntry('Type', $type);

        $subtype = Factory::create('Papier\Type\NameType', 'Type1');
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