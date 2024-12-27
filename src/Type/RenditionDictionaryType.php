<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\RenditionTypeValidator;

class RenditionDictionaryType extends DictionaryType
{
    /**
     * Set rendition type.
     *
     * @param string $type
     * @return RenditionDictionaryType
     */
    public function setS(string $type): RenditionDictionaryType
    {
        if (!RenditionTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("RenditionDictionaryType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\NameType', $type);

        $this->setEntry('S', $value);

        return $this;
    }

    /**
     * Set rendition name.
     *
     * @param  string  $N
     * @return RenditionDictionaryType
     */
    public function setN(string $N): RenditionDictionaryType
    {
        $value = Factory::create('Papier\Type\Base\NameType', $N);
        $this->setEntry('N', $value);
        return $this;
    }

    /**
     * Set "mush-honored" parameters.
     *
     * @param  DictionaryType  $MH
     * @return RenditionDictionaryType
     */
    public function setMH(DictionaryType $MH): RenditionDictionaryType
    {
        $this->setEntry('MH', $MH);
        return $this;
    }

    /**
     * Set "best-effort" parameters.
     *
     * @param  DictionaryType  $BE
     * @return RenditionDictionaryType
     */
    public function setBE(DictionaryType $BE): RenditionDictionaryType
    {
        $this->setEntry('BE', $BE);
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $type = Factory::create('Papier\Type\Base\NameType', 'Rendition');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}