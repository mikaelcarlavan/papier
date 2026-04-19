<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\PdfName;

/**
 * Rubber-stamp annotation (`/Subtype /Stamp`).
 *
 * Displays a predefined or custom stamp icon.  Standard icon names (§12.5.6.11
 * Table 181): `Approved`, `Experimental`, `NotApproved`, `AsIs`, `Expired`,
 * `NotForPublicRelease`, `Confidential`, `Final`, `Sold`, `Departmental`,
 * `ForComment`, `TopSecret`, `Draft`, `ForPublicRelease`.
 */
final class StampAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Stamp'; }

    /**
     * Set the stamp icon name (`/Name`).
     *
     * @param string $name  One of the standard stamp names listed above, or a
     *                      custom name for which an appearance stream is provided.
     */
    public function setIcon(string $name): static
    {
        $this->dict->set('Name', new PdfName($name));
        return $this;
    }
}
