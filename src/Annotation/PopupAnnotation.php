<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfBoolean, PdfObject};

/**
 * Pop-up window annotation (`/Subtype /Popup`).
 *
 * A pop-up is typically associated with a parent annotation (e.g. a
 * {@see TextAnnotation}) and shows or hides a resizable text window.
 */
final class PopupAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Popup'; }

    /**
     * Set the parent annotation reference (`/Parent`).
     *
     * @param PdfObject $parentRef  Indirect reference to the owning annotation.
     */
    public function setParent(PdfObject $parentRef): static
    {
        $this->dict->set('Parent', $parentRef);
        return $this;
    }

    /**
     * Set whether the pop-up is initially open (`/Open`).
     *
     * @param bool $open  true to show the pop-up on page load.
     */
    public function setOpen(bool $open): static
    {
        $this->dict->set('Open', new PdfBoolean($open));
        return $this;
    }
}
