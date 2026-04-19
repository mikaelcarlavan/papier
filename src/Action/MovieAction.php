<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfName, PdfObject, PdfString};

/**
 * Control playback of a movie annotation (`/S /Movie`).
 *
 * The target movie is identified by either an annotation reference or the
 * title (`/T`) of a {@see \Papier\Annotation\MovieAnnotation}.
 *
 * Note: the Movie annotation type is legacy in PDF 1.5+; prefer
 * {@see RenditionAction} with a {@see \Papier\Annotation\ScreenAnnotation}.
 *
 * Example:
 *
 *   $action = new MovieAction('Play', null, 'Demo Video');
 */
final class MovieAction extends Action
{
    /**
     * @param string          $operation   Playback command: `Play`, `Stop`, `Pause`, `Resume`.
     * @param PdfObject|null  $annotation  Indirect reference to the target MovieAnnotation.
     * @param string|null     $title       Title (`/T`) of the target annotation (alternative
     *                                     to $annotation).
     */
    public function __construct(
        string $operation = 'Play',
        ?PdfObject $annotation = null,
        ?string $title = null,
    ) {
        parent::__construct();
        $this->dict->set('Operation', new PdfName($operation));
        if ($annotation !== null) { $this->dict->set('Annotation', $annotation); }
        if ($title !== null)      { $this->dict->set('T', new PdfString($title)); }
    }

    public function getSubtype(): string { return 'Movie'; }
}
