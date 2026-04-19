<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfBoolean, PdfName, PdfString};

/**
 * Sticky-note annotation that displays a text pop-up (`/Subtype /Text`).
 *
 * The annotation itself is rendered as a small icon; clicking it opens a
 * pop-up window showing `/Contents`.
 *
 * Example:
 *
 *   $note = new TextAnnotation(72, 700, 92, 720);
 *   $note->setContents('Review this paragraph.')
 *        ->setIcon('Comment')
 *        ->setColor(1.0, 1.0, 0.0);  // yellow
 */
final class TextAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Text'; }

    /**
     * Set whether the annotation pop-up is initially open (`/Open`).
     *
     * @param bool $open  true to show the pop-up on page load.
     */
    public function setOpen(bool $open): static
    {
        $this->dict->set('Open', new PdfBoolean($open));
        return $this;
    }

    /**
     * Set the icon name (`/Name`).
     *
     * Standard values: `Comment`, `Help`, `Insert`, `Key`, `NewParagraph`,
     * `Note` (default), `Paragraph`.
     *
     * @param string $icon  Icon name.
     */
    public function setIcon(string $icon): static
    {
        $this->dict->set('Name', new PdfName($icon));
        return $this;
    }

    /**
     * Set the annotation state (`/State`).
     *
     * Used for review workflows.  Common values: `Marked`, `Unmarked`,
     * `Accepted`, `Rejected`, `Cancelled`, `Completed`, `None`.
     *
     * @param string $state  State string.
     */
    public function setState(string $state): static
    {
        $this->dict->set('State', new PdfString($state));
        return $this;
    }

    /**
     * Set the state model (`/StateModel`).
     *
     * @param string $model  `Marked` or `Review`.
     */
    public function setStateModel(string $model): static
    {
        $this->dict->set('StateModel', new PdfString($model));
        return $this;
    }
}
