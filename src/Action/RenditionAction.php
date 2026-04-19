<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfInteger, PdfObject};

/**
 * Control media rendition playback (`/S /Rendition`) (PDF 1.5+).
 *
 * Associates or dissociates a media rendition with a screen annotation.
 * The `/OP` (operation) entry controls the command:
 *   - `0` — Play (and associate rendition with the annotation).
 *   - `1` — Stop.
 *   - `2` — Pause.
 *   - `3` — Resume.
 *   - `4` — Play without associating.
 *
 * For the common case of embedding a video in a ScreenAnnotation, use
 * {@see \Papier\Annotation\ScreenAnnotation::setRendition()} which builds
 * this action internally.
 *
 * Example:
 *
 *   $action = new RenditionAction(0, $renditionDict, $screenAnnotRef);
 */
final class RenditionAction extends Action
{
    /**
     * @param int             $op          Operation code (0–4).
     * @param PdfObject|null  $rendition   Media rendition dictionary (`/R`).
     * @param PdfObject|null  $annotation  Indirect reference to the target
     *                                     ScreenAnnotation (`/AN`).
     */
    public function __construct(int $op, ?PdfObject $rendition = null, ?PdfObject $annotation = null)
    {
        parent::__construct();
        $this->dict->set('OP', new PdfInteger($op));
        if ($rendition  !== null) { $this->dict->set('R', $rendition); }
        if ($annotation !== null) { $this->dict->set('AN', $annotation); }
    }

    public function getSubtype(): string { return 'Rendition'; }
}
