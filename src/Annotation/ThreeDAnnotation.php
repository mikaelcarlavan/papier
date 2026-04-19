<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\PdfObject;

/**
 * Three-dimensional content annotation (`/Subtype /3D`) (PDF 1.6+).
 *
 * Embeds a 3D model (U3D or PRC format) in a rectangular viewport on the
 * page.  The model stream is stored in `/3DD`; activation parameters control
 * when and how the 3D view is activated.
 */
final class ThreeDAnnotation extends Annotation
{
    public function getSubtype(): string { return '3D'; }

    /**
     * Set the 3D stream or dictionary (`/3DD`).
     *
     * @param PdfObject $stream  A 3D stream (U3D or PRC) or a stream dictionary.
     */
    public function set3DStream(PdfObject $stream): static
    {
        $this->dict->set('3DD', $stream);
        return $this;
    }

    /**
     * Set the 3D activation dictionary (`/3DA`).
     *
     * Controls when (e.g. on page open) and how the 3D model activates.
     *
     * @param PdfObject $activation  3D activation dictionary.
     */
    public function setActivation(PdfObject $activation): static
    {
        $this->dict->set('3DA', $activation);
        return $this;
    }

    /**
     * Set the default view (`/3DV`).
     *
     * @param PdfObject $view  A 3D view dictionary or the name of a named view.
     */
    public function setDefaultView(PdfObject $view): static
    {
        $this->dict->set('3DV', $view);
        return $this;
    }
}
