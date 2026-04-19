<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

/**
 * Highlight (yellow marker) annotation (`/Subtype /Highlight`).
 *
 * The highlighted region is defined by one or more quad-point sets passed to
 * {@see self::setQuadPoints()}.  Each set of 8 values describes the four
 * corners of one highlighted word or run of text.
 */
final class HighlightAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Highlight'; }

    /**
     * Set the quad-points array (`/QuadPoints`).
     *
     * @param float[] $qp  Flat array of quad-point coordinates.
     *                     Each run of 8 values: x1,y1,x2,y2,x3,y3,x4,y4
     *                     (corners of the highlighted area, CCW from lower-left).
     */
    public function setQuadPoints(array $qp): static
    {
        $arr = new PdfArray();
        foreach ($qp as $v) { $arr->add(new PdfReal($v)); }
        $this->dict->set('QuadPoints', $arr);
        return $this;
    }
}
