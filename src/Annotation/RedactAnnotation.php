<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfArray, PdfBoolean, PdfReal, PdfString};

/**
 * Redaction annotation (`/Subtype /Redact`) (PDF 1.7+).
 *
 * Marks content for redaction.  After the redaction is applied (by the
 * appropriate software), the underlying content is permanently removed and
 * replaced by the overlay defined here.
 *
 * Example:
 *
 *   $redact = new RedactAnnotation(72, 680, 300, 700);
 *   $redact->setOverlayText('REDACTED')
 *          ->setFillColor(0, 0, 0);  // black fill after redaction
 */
final class RedactAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Redact'; }

    /**
     * Set the quad-points describing the redacted text regions (`/QuadPoints`).
     *
     * @param float[] $qp  Flat array; each run of 8 values defines one region.
     */
    public function setQuadPoints(array $qp): static
    {
        $arr = new PdfArray();
        foreach ($qp as $v) { $arr->add(new PdfReal($v)); }
        $this->dict->set('QuadPoints', $arr);
        return $this;
    }

    /**
     * Set the overlay text displayed after redaction (`/OverlayText`).
     *
     * @param string $text  Text to display in the redacted area (e.g. `REDACTED`).
     */
    public function setOverlayText(string $text): static
    {
        $this->dict->set('OverlayText', new PdfString($text));
        return $this;
    }

    /**
     * Set whether the overlay text should be tiled to fill the area (`/Repeat`).
     *
     * @param bool $r  true to tile; false for single instance.
     */
    public function setRepeat(bool $r): static
    {
        $this->dict->set('Repeat', new PdfBoolean($r));
        return $this;
    }

    /**
     * Set the fill colour applied to the redacted area (`/IC`).
     *
     * @param float $r  Red   [0, 1].
     * @param float $g  Green [0, 1].
     * @param float $b  Blue  [0, 1].
     */
    public function setFillColor(float $r, float $g, float $b): static
    {
        $ic = new PdfArray();
        $ic->add(new PdfReal($r));
        $ic->add(new PdfReal($g));
        $ic->add(new PdfReal($b));
        $this->dict->set('IC', $ic);
        return $this;
    }
}
