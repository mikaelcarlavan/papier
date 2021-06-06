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
     * Set name.
     *  
     * @param string $name
     * @return Type1FontType
     */
    public function setName(string $name): Type1FontType
    {
        $value = Factory::create('Name', $name);
        return $this->setEntry('Name', $value);
    }

    /**
     * Set basefont (PostScript) name.
     *  
     * @param string $name
     * @return Type1FontType
     */
    public function setBaseFont(string $name): Type1FontType
    {
        $value = Factory::create('Name', $name);
        return $this->setEntry('BaseFont', $value);
    }

    /**
     * Set first character code.
     *  
     * @param int $fc
     * @return Type1FontType
     */
    public function setFirstChar(int $fc): Type1FontType
    {
        $value = Factory::create('Integer', $fc);
        return $this->setEntry('FirstChar', $value);
    }

    /**
     * Set last character code.
     *  
     * @param int $lc
     * @return Type1FontType
     */
    public function setLastChar(int $lc): Type1FontType
    {
        $value = Factory::create('Integer', $lc);
        return $this->setEntry('LastChar', $value);
    }

    /**
     * Set widths.
     *  
     * @param ArrayObject $widths
     * @return Type1FontType
     */
    public function setWidths(ArrayObject $widths): Type1FontType
    {
        return $this->setEntry('Widths', $widths);
    }

    /**
     * Set font descriptor.
     *  
     * @param  DictionaryObject  $fd
     * @return Type1FontType
     */
    public function setFontDescriptor(DictionaryObject $fd): Type1FontType
    {
        return $this->setEntry('FontDescriptor', $fd);
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

        if (!$encoding instanceof DictionaryObject) {
            if (!EncodingValidator::isValid($encoding)) {
                throw new InvalidArgumentException("Encoding is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        $value = $encoding instanceof DictionaryObject ? $encoding : Factory::create('Name', $encoding);
        
        return $this->setEntry('Encoding', $value);
    }

    /**
     * Set map to unicde values.
     *  
     * @param  StreamObject  $tounicode
     * @return Type1FontType
     */
    public function setToUnicode(StreamObject $tounicode): Type1FontType
    {
        return $this->setEntry('ToUnicode', $tounicode);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Name', 'Font');
        $this->setEntry('Type', $type);

        $subtype = Factory::create('Name', 'Type1');
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