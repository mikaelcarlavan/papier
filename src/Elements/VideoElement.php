<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Annotation\Annotation;
use Papier\Annotation\ScreenAnnotation;
use Papier\Content\ContentStream;
use Papier\Multimedia\{MediaClip, MediaPlayParams, MediaRendition};
use Papier\Objects\{PdfArray, PdfReal};
use Papier\Structure\PdfResources;

/**
 * Video / audio player element — an inline media placeholder with click-to-play.
 *
 * Renders a dark rectangle representing a video frame (or audio player) and
 * attaches a {@see ScreenAnnotation} with a {@see MediaRendition} action that
 * triggers media playback when clicked.  The annotation is registered with
 * the page automatically via {@see AnnotationProvider}.
 *
 *   $clip = MediaClip::fromFile('intro.mp4', 'video/mp4');
 *
 *   $page->add(
 *       VideoElement::create($clip, 72, 400, 300, 170)
 *           ->label('▶  intro.mp4')
 *           ->bgColor(Color::rgb(0.05, 0.05, 0.05))
 *           ->playParams(
 *               (new MediaPlayParams())->setAutoPlay(false)->setShowControls(true)
 *           ),
 *   );
 *
 * The media file can be an external reference (recommended for video — keeps
 * the PDF small) or an embedded file (self-contained, larger PDF).  See
 * {@see MediaClip} and {@see \Papier\Multimedia\FileSpec}.
 *
 * @see MediaClip         To build the media asset reference.
 * @see MediaPlayParams   To control playback behaviour.
 * @see MediaRendition    The rendition object linking clip to params.
 */
final class VideoElement implements Element, AnnotationProvider
{
    private ?string          $label      = null;
    private ?string          $fontName   = null;
    private float            $fontSize   = 11;
    private Color            $bgColor;
    private ?MediaPlayParams $playParams = null;
    private string           $title      = '';

    private readonly ScreenAnnotation $annotation;

    private function __construct(
        private readonly MediaClip $clip,
        private readonly float     $x,
        private readonly float     $y,
        private readonly float     $w,
        private readonly float     $h,
    ) {
        $this->bgColor    = Color::rgb(0.05, 0.05, 0.05);
        $this->annotation = new ScreenAnnotation($x, $y, $x + $w, $y + $h);
    }

    /**
     * Create a video element at (x, y) with width × height in points.
     *
     * @param MediaClip $clip  The media asset to play (video or audio).
     * @param float     $x     Lower-left X in points.
     * @param float     $y     Lower-left Y in points.
     * @param float     $w     Width in points.
     * @param float     $h     Height in points.
     */
    public static function create(
        MediaClip $clip,
        float $x,
        float $y,
        float $w,
        float $h,
    ): self {
        return new self($clip, $x, $y, $w, $h);
    }

    /**
     * Set the text label rendered in the centre of the placeholder.
     *
     * @param string      $label     Label text (e.g. `'▶  intro.mp4'`).
     * @param string|null $fontName  Resource name (e.g. `'F1'`); required to render the label.
     * @param float       $fontSize  Font size in points (default 11).
     */
    public function label(string $label, ?string $fontName = null, float $fontSize = 11): self
    {
        $clone           = clone $this;
        $clone->label    = $label;
        $clone->fontName = $fontName;
        $clone->fontSize = $fontSize;
        return $clone;
    }

    /**
     * Set the background colour of the placeholder rectangle.
     *
     * @param Color $color  Background colour (default near-black).
     */
    public function bgColor(Color $color): self
    {
        $clone          = clone $this;
        $clone->bgColor = $color;
        return $clone;
    }

    /**
     * Set playback parameters for the rendition.
     *
     * @param MediaPlayParams $params  Volume, auto-play, controls, looping, etc.
     */
    public function playParams(MediaPlayParams $params): self
    {
        $clone             = clone $this;
        $clone->playParams = $params;
        return $clone;
    }

    /**
     * Set the human-readable title shown in the viewer's media panel.
     *
     * @param string $title  Title string.
     */
    public function title(string $title): self
    {
        $clone        = clone $this;
        $clone->title = $title;
        return $clone;
    }

    // ── Element + AnnotationProvider ─────────────────────────────────────────

    public function render(ContentStream $cs, PdfResources $resources): void
    {
        // Draw the placeholder background
        $cs->save();
        $this->bgColor->applyFill($cs);
        $cs->drawRect($this->x, $this->y, $this->w, $this->h, true, false);
        $cs->restore();

        // Draw label in the centre
        if ($this->label !== null && $this->fontName !== null) {
            $cs->save();
            Color::white()->applyFill($cs);
            $cs->beginText()
               ->setFont($this->fontName, $this->fontSize)
               ->setTextPosition(
                   $this->x + 8,
                   $this->y + $this->h / 2 - $this->fontSize / 2,
               )
               ->showText($this->label)
               ->endText();
            $cs->restore();
        }

        // Build rendition and attach to annotation
        $rendition = new MediaRendition($this->clip, $this->title ?: null);
        if ($this->playParams !== null) {
            $rendition->setPlayParams($this->playParams);
        }
        if ($this->title !== '') {
            $this->annotation->setTitle($this->title);
        }
        $this->annotation->setRendition($rendition->getDictionary());
    }

    /** @return Annotation[] */
    public function getAnnotations(): array
    {
        return [$this->annotation];
    }
}
