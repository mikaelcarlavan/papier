<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;
use Papier\Validator\ColourComponentsValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\StringValidator;
use RuntimeException;


class ICCBasedColourSpaceStreamType extends StreamObject
{
 
    /**
     * Set number of colour components.
     *  
     * @param int $N
     * @return ICCBasedColourSpaceStreamType
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setN(int $N): ICCBasedColourSpaceStreamType
    {
        if (!ColourComponentsValidator::isValid($N)) {
            throw new InvalidArgumentException("N is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $N);

        $this->setEntry('N', $value);
        return $this;
    } 

    /**
     * Set alternate color space.
     *  
     * @param  mixed  $alternate
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return ICCBasedColourSpaceStreamType
     */
    public function setAlternate($alternate): ICCBasedColourSpaceStreamType
    {
        if (!StringValidator::isValid($alternate) && !$alternate instanceof ArrayObject) {
            throw new InvalidArgumentException("Alternate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $alternate instanceof ArrayObject ? $alternate : Factory::create('Papier\Type\Base\NameType', $alternate);

        $this->setEntry('Alternate', $value);
        return $this;
    } 

     /**
     * Set the range of colour components.
     *  
     * @param array<float> $range
     * @return ICCBasedColourSpaceStreamType
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     */
    public function setRange(array $range): ICCBasedColourSpaceStreamType
    {
        if (!NumbersArrayValidator::isValid($range)) {
            throw new InvalidArgumentException("Range is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumbersArrayType', $range);

        $this->setEntry('Range', $value);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param  StreamObject  $metadata
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return ICCBasedColourSpaceStreamType
     */
    public function setMetadata(StreamObject $metadata): ICCBasedColourSpaceStreamType
    {
        $this->setEntry('Metadata', $metadata);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        throw new RuntimeException("ICCBased colour space is not implemented in this version. See ".__CLASS__." class's documentation for possible values.");

        /*if (!$this->hasEntry('N')) {
            throw new RuntimeException("N is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Range') && count($this->getEntry('Range')) != 2 * $this->getEntry('N')->getValue()) {
            throw new RuntimeException("Range size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }*

        return parent::format();
        */
    }
}