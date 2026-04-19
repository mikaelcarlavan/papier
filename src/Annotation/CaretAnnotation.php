<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\PdfName;

/**
 * Text-insertion caret annotation (`/Subtype /Caret`).
 *
 * Indicates a position in the text where content should be inserted.
 */
final class CaretAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Caret'; }

    /**
     * Set the caret symbol (`/Sy`).
     *
     * @param string $sym  `P` (paragraph symbol) or `None`.
     */
    public function setSymbol(string $sym): static
    {
        $this->dict->set('Sy', new PdfName($sym));
        return $this;
    }
}
