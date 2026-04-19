<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

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
