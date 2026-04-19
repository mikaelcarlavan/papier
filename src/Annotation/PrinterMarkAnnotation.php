<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\PdfString;

/**
 * Printer registration / crop mark annotation (`/Subtype /PrinterMark`).
 *
 * Used to add printer marks (crop marks, colour bars, registration targets)
 * to a page.  Not visible to end users; requires `PrinterMark` flag (bit 12)
 * in `/F`.
 */
final class PrinterMarkAnnotation extends Annotation
{
    public function getSubtype(): string { return 'PrinterMark'; }

    /**
     * Set the mark name (`/MN`).
     *
     * @param string $mn  Human-readable name for the printer mark.
     */
    public function setMarkName(string $mn): static
    {
        $this->dict->set('MN', new PdfString($mn));
        return $this;
    }
}
