<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Object\FunctionObject;
use Papier\Object\RenditionObject;
use Papier\Validator\FunctionTypeValidator;
use Papier\Validator\PaintTypeValidator;
use InvalidArgumentException;
use Papier\Validator\RenditionTypeValidator;

class RenditionType extends DictionaryType
{
    /**
     * Media rendition type
     *
     * @var string
     */
    const MEDIA_RENDITION_TYPE = 'MR';

    /**
     * Selector rendition type
     *
     * @var string
     */
    const SELECTOR_RENDITION_TYPE = 'SR';

    /**
     * Set rendition type.
     *
     * @param string $type
     * @return RenditionType
     */
    public function setS(string $type): RenditionType
    {
        if (!RenditionTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("RenditionType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $type);

        $this->setEntry('S', $value);

        return $this;
    }

    /**
     * Set rendition name.
     *
     * @param  string  $N
     * @return RenditionType
     */
    public function setN(string $N): RenditionType
    {
        $value = Factory::create('Papier\Type\NameType', $N);
        $this->setEntry('N', $value);
        return $this;
    }

    /**
     * Set "mush-honored" parameters.
     *
     * @param  DictionaryObject  $MH
     * @return RenditionType
     */
    public function setMH(DictionaryObject $MH): RenditionType
    {
        $this->setEntry('MH', $MH);
        return $this;
    }

    /**
     * Set "best-effort" parameters.
     *
     * @param  DictionaryObject  $BE
     * @return RenditionType
     */
    public function setBE(DictionaryObject $BE): RenditionType
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
        $type = Factory::create('Papier\Type\NameType', 'Type');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}