<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

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
     * @param float $r  Red   [0, 1].
     * @param float $g  Green [0, 1].
     * @param float $b  Blue  [0, 1].
     */
    public function setInteriorColor(float $r, float $g, float $b): static
    {
        $ic = new PdfArray();
        $ic->add(new PdfReal($r));
        $ic->add(new PdfReal($g));
        $ic->add(new PdfReal($b));
        $this->dict->set('IC', $ic);
        return $this;
    }
}
