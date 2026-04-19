<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfDictionary, PdfInteger, PdfName, PdfObject, PdfString};

/**
 * Screen annotation for media renditions (`/Subtype /Screen`) (PDF 1.5+).
 *
 * A screen annotation defines a region on the page where a media clip
 * (video, audio) can be played.  It is activated by a RenditionAction stored
 * in `/A`, which is built automatically by {@see self::setRendition()}.
 *
 * For a higher-level API that handles placement + annotation in one call, use
 * {@see \Papier\Elements\VideoElement} with `$page->add()`.
 *
 * Example:
 *
 *   $clip     = MediaClip::fromFile('demo.mp4', 'video/mp4');
 *   $rendition = new MediaRendition($clip, 'Demo Video');
 *   $screen   = new ScreenAnnotation(72, 400, 372, 625);
 *   $screen->setTitle('Demo Video')
 *          ->setRendition($rendition->getDictionary());
 *   $page->addAnnotation($screen);
 */
final class ScreenAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Screen'; }

    /**
     * Set the screen title (`/T`).
     *
     * @param string $t  Title shown in the viewer's UI for this media widget.
     */
    public function setTitle(string $t): static
    {
        $this->dict->set('T', new PdfString($t));
        return $this;
    }

    /**
     * Attach a media rendition via a RenditionAction (§12.6.4.17).
     *
     * Builds a RenditionAction (`/S /Rendition`, `/OP 0`) and writes it to
     * `/A`.  For strict ISO compliance the action's `/AN` should be an indirect
     * reference to this annotation itself — pass $selfRef when available.
     *
     * @param PdfObject      $rendition  Pass {@see \Papier\Multimedia\MediaRendition::getDictionary()}.
     * @param PdfObject|null $selfRef    Indirect reference back to this annotation (optional).
     */
    public function setRendition(PdfObject $rendition, ?PdfObject $selfRef = null): static
    {
        $action = new PdfDictionary();
        $action->set('Type', new PdfName('Action'));
        $action->set('S',    new PdfName('Rendition'));
        $action->set('OP',   new PdfInteger(0));
        $action->set('R',    $rendition);
        if ($selfRef !== null) {
            $action->set('AN', $selfRef);
        }
        $this->dict->set('A', $action);
        return $this;
    }
}
