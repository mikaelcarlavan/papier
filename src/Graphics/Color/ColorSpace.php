<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\PdfObject;

/**
 * Abstract colour space (ISO 32000-1 §8.6).
 *
 * The colour model determines how numeric values are converted to colour.
 * Device, CIE-based, special, and pattern colour spaces are all subclasses.
 */
abstract class ColorSpace
{
    abstract public function getName(): string;

    /** Number of colour components for this space. */
    abstract public function getComponentCount(): int;

    /** Return the PDF object representation (name or array). */
    abstract public function toPdfObject(): PdfObject;
}
