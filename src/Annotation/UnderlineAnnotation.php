<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

/**
 * Underline annotation (`/Subtype /Underline`).
 *
 * Draws a line under the text spans described by {@see self::setQuadPoints()}.
 */
final class UnderlineAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Underline'; }

    /**
     * Set the quad-points array (`/QuadPoints`).
     *
     * @param float[] $qp  Flat array; each run of 8 values defines one span.
     */
    public function setQuadPoints(array $qp): static
    {
        $arr = new PdfArray();
        foreach ($qp as $v) { $arr->add(new PdfReal($v)); }
        $this->dict->set('QuadPoints', $arr);
        return $this;
    }
}
