<?php

namespace Papier\File;

use Papier\Base\Object;
use Papier\Validator\IntValidator;
use Papier\Validator\BoolValidator;


use InvalidArgumentException;

class FileHeader extends Object
{
    /**
     * Minimum allowable value of header's version.
     *
     * @var int
     */
    private $minVersion = 0;

    /**
     * Maximal allowable value of header's version.
     *
     * @var int
     */
    private $maxVersion = 7;

    /**
     * Bool which indicates if file has binary data.
     *
     * @var bool
     */
    private $hasBinaryData = true;  
    /**
     * Format header's content.
     *
     * @return string
     */
    public function format()
    {
        $value = sprintf("%%PDF-1.%d", $this->getVersion());
        if ($this->getHasBinaryData()) {
            $comment = new Comment();
            $chars = array_map('chr', range(128, 131));
            $value .= $comment->setValue(implode('', $chars))->format();
        }
        return $value;
    }

    /**
     * Get header's version.
     *
     * @return int
     */
    protected function getVersion()
    {
        return $this->getValue() ?? 0;
    }

    /**
     * Set header's version.
     *  
     * @param  int  $version
     * @return \Papier\File\FileHeader
     */
    protected function setVersion($version)
    {
        if (!IntValidator::isValid($version, $this->minVersion, $this->maxVersion)) {
            throw new InvalidArgumentException("Version is incorrect. See FileHeader class's documentation for possible values.");
        }

        return parent::setValue($value);
    } 

    /**
     * Get if file has binary data.
     *
     * @return int
     */
    protected function getHasBinaryData()
    {
        return $this->hasBinaryData;
    }

    /**
     * Set if file has bianry data.
     *  
     * @param  bool  $hasBinaryData
     * @return \Papier\File\FileHeader
     */
    protected function setHasBinaryData($hasBinaryData)
    {
        if (!BoolValidator::isValid($hasBinaryData)) {
            throw new InvalidArgumentException("HasBinaryData is incorrect. See FileHeader class's documentation for possible values.");
        }

        $this->hasBinaryData = $hasBinaryData;
        return $this;
    } 
}