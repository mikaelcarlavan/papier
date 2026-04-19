<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Elements\Color;
use Papier\Objects\PdfArray;

/**
 * Ellipse outline annotation (`/Subtype /Circle`).
 *
 * Despite the name, this annotation can render any ellipse bounded by its
 * `/Rect`.  Use {@see self::setInteriorColor()} for the fill.
 */
final class CircleAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Circle'; }

    /**
     * Set the interior fill colour (`/IC`).
     *
     * Supports all device colour spaces: use {@see Color::gray()},
     * {@see Color::rgb()}, {@see Color::hex()}, or {@see Color::cmyk()}.
     * Pass an empty `Color` equivalent by calling {@see clearInteriorColor()}.
     *
     * @param Color $color  Fill colour for the ellipse interior.
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
