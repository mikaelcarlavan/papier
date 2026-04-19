<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfDictionary, PdfName, PdfString};

/**
 * Media rendition (ISO 32000-1 §13.2.2).
 *
 * A rendition is the top-level object that ties together a MediaClip and its
 * playback parameters.  It is referenced by a RenditionAction attached to a
 * Screen annotation.
 *
 * Flow:
 *   MediaClip      — what to play (file reference + MIME type)
 *   MediaPlayParams — how to play it (volume, autoplay, controls, looping)
 *   MediaRendition  — combines clip + params
 *   RenditionAction — triggers the rendition from a Screen annotation
 *
 * Usage:
 *   $clip    = MediaClip::fromFile('intro.mp4', 'video/mp4');
 *   $params  = (new MediaPlayParams())->setAutoPlay(true)->setShowControls(true);
 *   $rend    = (new MediaRendition($clip, 'Intro Video'))->setPlayParams($params);
 *
 *   $action  = new RenditionAction(0, $rend->getDictionary());
 *   $screen  = new ScreenAnnotation(x1, y1, x2, y2);
 *   $screen->setTitle('Intro Video')->setAction($action->getDictionary());
 */
final class MediaRendition
{
    private PdfDictionary $dict;

    /**
     * @param MediaClip   $clip  The media asset to play.
     * @param string|null $name  Human-readable name (shown in some viewers).
     */
    public function __construct(MediaClip $clip, ?string $name = null)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Rendition'));
        $this->dict->set('S',    new PdfName('MR'));

        if ($name !== null) {
            $this->dict->set('N', new PdfString($name));
        }

        $this->dict->set('C', $clip->getDictionary());
    }

    /** Attach playback parameters (volume, controls, looping, auto-play). */
    public function setPlayParams(MediaPlayParams $params): static
    {
        $this->dict->set('P', $params->getDictionary());
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
