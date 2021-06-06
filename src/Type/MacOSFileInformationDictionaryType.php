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
     * @return MacOSFileInformationDictionaryType
     */
    public function setSubtype($subtype): MacOSFileInformationDictionaryType
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
     * @param string $creator
     * @return MacOSFileInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setCreator(string $creator): MacOSFileInformationDictionaryType
    {
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
     * @param  StreamObject  $resfork
     * @throws InvalidArgumentException if the provided argument is not of type 'StreamObject'.
     * @return MacOSFileInformationDictionaryType
     */
    public function setResFork(StreamObject $resfork): MacOSFileInformationDictionaryType
    {
        if (!$resfork instanceof StreamObject) {
            throw new InvalidArgumentException("ResFork is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('ResFork', $resfork);
        return $this;
    }
}