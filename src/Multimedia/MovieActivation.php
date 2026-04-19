<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfBoolean, PdfDictionary, PdfName, PdfReal};

/**
 * Movie activation dictionary (ISO 32000-1 §13.4.4).
 *
 * Controls how a Movie annotation plays: start/duration offsets, rate,
 * volume, floating-window placement, and playback mode.
 *
 * Usage:
 *   $act = (new MovieActivation())
 *       ->setVolume(0.8)
 *       ->setShowControls(true)
 *       ->setMode(MovieActivation::MODE_REPEAT);
 *
 *   $annot = new MovieAnnotation(x1, y1, x2, y2);
 *   $annot->setActivation($act->getDictionary());
 */
final class MovieActivation
{
    public const MODE_ONCE       = 'Once';
    public const MODE_OPEN       = 'Open';
    public const MODE_REPEAT     = 'Repeat';
    public const MODE_PALINDROME = 'Palindrome';

    private PdfDictionary $dict;

    public function __construct()
    {
        $this->dict = new PdfDictionary();
    }

    /**
     * Playback rate relative to the movie's normal rate.
     * 1.0 = normal speed; 2.0 = double; −1.0 = reverse.
     */
    public function setRate(float $rate): static
    {
        $this->dict->set('Rate', new PdfReal($rate));
        return $this;
    }

    /**
     * Volume from −1.0 (mute) to 1.0 (maximum).
     * Values outside [−1, 1] are clipped.
     */
    public function setVolume(float $volume): static
    {
        $this->dict->set('Volume', new PdfReal(max(-1.0, min(1.0, $volume))));
        return $this;
    }

    /** Whether to show the media player controls during playback. */
    public function setShowControls(bool $show): static
    {
        $this->dict->set('ShowControls', new PdfBoolean($show));
        return $this;
    }

    /**
     * Playback mode:
     *   Once       — play once, then close
     *   Open       — play once, keep player open
     *   Repeat     — loop indefinitely
     *   Palindrome — play forward, then backward, repeat
     */
    public function setMode(string $mode): static
    {
        $this->dict->set('Mode', new PdfName($mode));
        return $this;
    }

    /**
     * If true, the viewer halts until the movie finishes before
     * processing other actions.
     */
    public function setSynchronous(bool $sync): static
    {
        $this->dict->set('Synchronous', new PdfBoolean($sync));
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
