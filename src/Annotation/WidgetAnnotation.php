<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfName, PdfString};

/**
 * Form-field widget annotation (`/Subtype /Widget`).
 *
 * Widget annotations provide the visual representation of AcroForm fields.
 * In the merged field+widget approach (§12.7.3.3) used by the
 * {@see \Papier\AcroForm\FormField} classes, the widget and field dictionaries
 * are combined into one object — you rarely need to instantiate this class
 * directly.
 */
final class WidgetAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Widget'; }

    /**
     * Set the highlight mode (`/H`).
     *
     * @param string $h  `N` none, `I` invert, `O` outline, `P` push, `T` toggle.
     */
    public function setHighlightMode(string $h): static
    {
        $this->dict->set('H', new PdfName($h));
        return $this;
    }

    /**
     * Set the default appearance string (`/DA`).
     *
     * @param string $da  PDF content-stream fragment, e.g. `/Helv 12 Tf 0 g`.
     */
    public function setDefaultAppearance(string $da): static
    {
        $this->dict->set('DA', new PdfString($da));
        return $this;
    }
}
