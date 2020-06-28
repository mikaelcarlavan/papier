<?php

namespace Papier\File;

use Papier\Base\Object;
use Papier\Validator\IntValidator;

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
     * Format header's content.
     *
     * @return string
     */
    public function format($hasBinaryData = true)
    {
        $value = sprintf("%%PDF-1.%d", $this->getVersion());
        if ($hasBinaryData) {
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
}