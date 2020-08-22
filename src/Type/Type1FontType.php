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
     * @param  string  $name
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\Type1FontType
     */
    public function setName($name)
    {
        if (!StringValidator::isValid($name)) {
            throw new InvalidArgumentException("Name is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $name);
        return $this->setEntry('Name', $value);
    }

    /**
     * Set basefont (PostScript) name.
     *  
     * @param  string  $name
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\Type1FontType
     */
    public function setBaseFont($name)
    {
        if (!StringValidator::isValid($name)) {
            throw new InvalidArgumentException("BaseFont is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $name);
        return $this->setEntry('BaseFont', $value);
    }

    /**
     * Set first character code.
     *  
     * @param  int  $fc
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\Type1FontType
     */
    public function setFirstChar($fc)
    {
        if (!IntegerValidator::isValid($fc)) {
            throw new InvalidArgumentException("FirstChar is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $fc);
        return $this->setEntry('FirstChar', $value);
    }

    /**
     * Set last character code.
     *  
     * @param  int  $lc
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\Type1FontType
     */
    public function setLastChar($lc)
    {
        if (!IntegerValidator::isValid($lc)) {
            throw new InvalidArgumentException("LastChar is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $lc);
        return $this->setEntry('LastChar', $value);
    }

    /**
     * Set widths.
     *  
     * @param  \Papier\Object\ArrayObject  $widths
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\Type1FontType
     */
    public function setWidths($widths)
    {
        if (!$widths instanceof ArrayObject) {
            throw new InvalidArgumentException("Widths is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return $this->setEntry('Widths', $widths);
    }

    /**
     * Set font descriptor.
     *  
     * @param  \Papier\Object\DictionaryObject  $fd
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
     * @return \Papier\Type\Type1FontType
     */
    public function setFontDescriptor($fd)
    {
        if (!$fd instanceof DictionaryObject) {
            throw new InvalidArgumentException("FontDescriptor is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return $this->setEntry('FontDescriptor', $fd);
    }

    /**
     * Set encoding.
     *  
     * @param  mixed  $encoding
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'string'.
     * @return \Papier\Type\Type1FontType
     */
    public function setEncoding($encoding)
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
     * @param  \Papier\Object\StreamObject  $encoding
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\Type1FontType
     */
    public function setToUnicode($tounicode)
    {
        if (!$tounicode instanceof StreamObject) {
            throw new InvalidArgumentException("Encoding is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        return $this->setEntry('ToUnicode', $tounicode);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
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