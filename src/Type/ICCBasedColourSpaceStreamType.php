<?php

namespace Papier\Type;

use Papier\Object\StreamObject;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;
use Papier\Object\NameObject;

use Papier\Validator\ColourComponentsValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\BitsPerComponentValidator;
use Papier\Validator\RenderingIntentValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class ICCBaseColourSpaceStreamType extends StreamObject
{
 
    /**
     * Set number of colour components.
     *  
     * @param  int  $N
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Type\ICCBaseColourSpaceStreamType
     */
    public function setN($N)
    {
        if (!ColourComponentsValidator::isValid($N)) {
            throw new InvalidArgumentException("N is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $N);

        $this->setEntry('N', $value);
        return $this;
    } 

    /**
     * Set alternate color space.
     *  
     * @param  mixed  $alternate
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return \Papier\Type\ICCBaseColourSpaceStreamType
     */
    public function setAlternate($alternate)
    {
        if (!StringValidator::isValid($alternate) && !$alternate instanceof ArrayObject) {
            throw new InvalidArgumentException("Alternate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $alternate instanceof ArrayObject ? $alternate : Factory::create('Name', $alternate);

        $this->setEntry('Alternate', $value);
        return $this;
    } 

     /**
     * Set the range of colour components.
     *  
     * @param  array  $range
     * @throws InvalidArgumentException if the provided argument is not an array of type 'int' or 'float'.
     * @return \Papier\Type\ICCBaseColourSpaceStreamType
     */
    public function setRange($range)
    {
        if (!NumbersArrayValidator::isValid($range)) {
            throw new InvalidArgumentException("Range is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('NumbersArray', $range);

        $this->setEntry('Range', $value);
        return $this;
    }

    /**
     * Set metadata.
     *  
     * @param  \Papier\Object\StreamObject  $metadata
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\ICCBaseColourSpaceStreamType
     */
    public function setMetadata($metadata)
    {
        if (!$metadata instanceof StreamObject) {
            throw new InvalidArgumentException("Metadata is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Metadata', $metadata);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        throw new RuntimeException("ICCBased colour space is not implemented in this version. See ".__CLASS__." class's documentation for possible values.");

        if (!$this->hasEntry('N')) {
            throw new RuntimeException("N is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($this->hasEntry('Range') && count($this->getEntry('Range')) != 2 * $this->getEntry('N')->getValue()) {
            throw new RuntimeException("Range size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}