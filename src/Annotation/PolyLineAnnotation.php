<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

/**
 * Open polyline annotation (`/Subtype /PolyLine`).
 *
 * Like {@see PolygonAnnotation} but the path is not closed.  Arrowhead
 * styles can be applied to the endpoints.
 */
final class PolyLineAnnotation extends Annotation
{
    public function getSubtype(): string { return 'PolyLine'; }

    /**
     * Set polyline vertices (`/Vertices`).
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
