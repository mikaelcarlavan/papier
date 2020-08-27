<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;

use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class MacOSFileInformationDictionaryType extends DictionaryObject
{
    /**
     * Set subtype.
     *  
     * @param  string  $subtype
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\MacOSFileInformationDictionaryType
     */
    public function setSubtype($subtype)
    {
        if (!StringValidator::isValid($subtype)) {
            throw new InvalidArgumentException("Subtype is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $hex = '';
        foreach (str_split($subtype) as $s) {
            $hex.= dechex(ord($s));
        }

        $value = Factory::create('Integer', hexdec($hex));

        $this->setEntry('Subtype', $value);
        return $this;
    } 

    /**
     * Set creator.
     *  
     * @param  string  $creator
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\MacOSFileInformationDictionaryType
     */
    public function setCreator($creator)
    {
        if (!StringValidator::isValid($creator)) {
            throw new InvalidArgumentException("Creator is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $hex = '';
        foreach (str_split($creator) as $s) {
            $hex.= dechex(ord($s));
        }

        $value = Factory::create('Integer', hexdec($hex));

        $this->setEntry('Creator', $value);
        return $this;
    } 

    /**
     * Set binary content of resource fork.
     *  
     * @param  \Papier\Object\StreamObject  $resfork
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return \Papier\Type\MacOSFileInformationDictionaryType
     */
    public function setResFork($resfork)
    {
        if (!$resfork instanceof StreamObject) {
            throw new InvalidArgumentException("ResFork is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ResFork', $resfork);
        return $this;
    }
}