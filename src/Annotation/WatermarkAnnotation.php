<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName};

/**
 * Fixed-position watermark annotation (`/Subtype /Watermark`).
 *
 * A watermark annotation is printed at a fixed position regardless of page
 * scaling, making it useful for "DRAFT" / "CONFIDENTIAL" overlays.
 */
final class WatermarkAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Watermark'; }

    /**
     * Enable fixed-print mode (`/FixedPrint`).
     *
     * When true, the annotation is rendered at a fixed scale relative to the
     * page media, ignoring viewer scaling.
     *
     * @param bool $fp  true to enable fixed-print behaviour.
     */
    public function setFixedPrint(bool $fp = true): static
    {
        $fpDict = new PdfDictionary();
        $fpDict->set('Type', new PdfName('FixedPrint'));
        $fpDict->set('Matrix', new PdfArray());
        $this->dict->set('FixedPrint', $fpDict);
        return $this;
    }
}
