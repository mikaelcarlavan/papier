<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfReal};

/**
 * Media play parameters (ISO 32000-1 §13.2.5).
 *
 * Attached to a MediaRendition via /P.  Controls volume, looping, fit mode,
 * auto-play, and whether the player UI is visible.
 *
 * Fit styles for /F:
 *   1 = meet      — scale proportionally to fit entirely within the rect
 *   2 = slice     — scale proportionally to fill the rect (may clip)
 *   3 = fill      — stretch to fill the rect exactly (ignores aspect ratio)
 *   4 = scroll    — no scaling; scrollbars if needed
 *   5 = hidden    — not displayed
 *   6 = tightest  — use the tightest fit for all media types
 *
 * Usage:
 *   $params = (new MediaPlayParams())
 *       ->setVolume(80)
 *       ->setShowControls(true)
 *       ->setAutoPlay(true)
 *       ->setRepeatCount(0.0); // loop forever
 */
final class MediaPlayParams
{
    /** Scale proportionally to fit entirely within the annotation rectangle. */
    public const FIT_MEET     = 1;
    /** Scale proportionally to fill the rectangle (may clip edges). */
    public const FIT_SLICE    = 2;
    /** Stretch to fill the rectangle exactly, ignoring aspect ratio. */
    public const FIT_FILL     = 3;
    /** No scaling; show scrollbars if media is larger than the rectangle. */
    public const FIT_SCROLL   = 4;
    /** Not displayed. */
    public const FIT_HIDDEN   = 5;
    /** Use the tightest fit across all media types in the rendition. */
    public const FIT_TIGHTEST = 6;

    private PdfDictionary $dict;

    public function __construct()
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('MediaPlayParams'));
    }

    /**
     * Volume: 0 (silent) to 100 (full volume).
     * Clipped to [0, 100].
     */
    public function setVolume(int $volume): static
    {
        $this->dict->set('V', new PdfInteger(max(0, min(100, $volume))));
        return $this;
    }

    /** Whether to display media player controls (play/pause/seek bar). */
    public function setShowControls(bool $show): static
    {
        $this->dict->set('C', new PdfBoolean($show));
        return $this;
    }

    /**
     * How the media is scaled to fit the annotation rectangle.
     *
     * Use the `FIT_*` class constants:
     * `FIT_MEET`, `FIT_SLICE`, `FIT_FILL`, `FIT_SCROLL`, `FIT_HIDDEN`, `FIT_TIGHTEST`.
     */
    public function setFitStyle(int $fit): static
    {
        $this->dict->set('F', new PdfInteger($fit));
        return $this;
    }

    /**
     * Repeat count.  0.0 = loop indefinitely.
     * Fractional values (e.g. 1.5) play 1.5 times.
     */
    public function setRepeatCount(float $count): static
    {
        $this->dict->set('RC', new PdfReal($count));
        return $this;
    }

    /** Start playing automatically when the page is displayed. */
    public function setAutoPlay(bool $auto): static
    {
        $this->dict->set('A', new PdfBoolean($auto));
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
