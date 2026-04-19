<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfReal};

/**
 * Freehand ink annotation (`/Subtype /Ink`).
 *
 * Renders one or more open, free-form freehand strokes (ink paths).  Each
 * stroke is an array of alternating X/Y coordinate pairs.
 *
 * Example:
 *
 *   $ink = new InkAnnotation(60, 60, 200, 200);
 *   $ink->setInkList([[70, 120, 100, 160, 130, 110, 160, 150]]);
 */
final class InkAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Ink'; }

    /**
     * Set the ink path lists (`/InkList`).
     *
     * @param float[][] $lists  Array of strokes; each stroke is a flat array of
     *                          alternating X/Y coordinates: [x1,y1,x2,y2,…].
     */
    public function setInkList(array $lists): static
    {
        $inkList = new PdfArray();
        foreach ($lists as $path) {
            $pathArr = new PdfArray();
            foreach ($path as $v) { $pathArr->add(new PdfReal($v)); }
            $inkList->add($pathArr);
        }
        $this->dict->set('InkList', $inkList);
        return $this;
    }
}
