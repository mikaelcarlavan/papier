<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

/**
 * Closed polygon annotation (`/Subtype /Polygon`).
 *
 * Renders a filled or stroked closed polygon defined by a flat array of
 * alternating X/Y coordinates passed to {@see self::setVertices()}.
 */
final class PolygonAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Polygon'; }

    /**
     * Set polygon vertices (`/Vertices`).
     *
     * @param float[] $vertices  Flat array: [x1, y1, x2, y2, …].
     */
    public function setVertices(array $vertices): static
    {
        $arr = new PdfArray();
        foreach ($vertices as $v) { $arr->add(new PdfReal($v)); }
        $this->dict->set('Vertices', $arr);
        return $this;
    }
}
