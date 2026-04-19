<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Elements\Color;
use Papier\Objects\PdfArray;

/**
 * Rectangle outline annotation (`/Subtype /Square`).
 *
 * Draws a rectangle coinciding with (or inset from) the annotation's `/Rect`.
 * Use {@see Annotation::setColor()} for the stroke colour and
 * {@see self::setInteriorColor()} for the fill.
 */
final class SquareAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Square'; }

    /**
     * Set the interior fill colour (`/IC`).
     *
     * Supports all device colour spaces: use {@see Color::gray()},
     * {@see Color::rgb()}, {@see Color::hex()}, or {@see Color::cmyk()}.
     *
     * @param Color $color  Fill colour for the rectangle interior.
     */
    public function setInteriorColor(Color $color): static
    {
        $this->dict->set('IC', $this->colorToArray($color));
        return $this;
    }

    /**
     * Remove the interior fill (transparent interior).
     */
    public function clearInteriorColor(): static
    {
        $this->dict->set('IC', new PdfArray());
        return $this;
    }
}
