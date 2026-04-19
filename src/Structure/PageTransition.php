<?php

declare(strict_types=1);

namespace Papier\Structure;

use Papier\Objects\{PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfReal};

/**
 * Page transition dictionary (ISO 32000-1 §12.4.4).
 *
 * A page transition specifies the visual effect used when the viewer moves
 * to a page in presentation (full-screen) mode.  Attach a transition to a
 * page with {@see PdfPage::setTransition()}.  To auto-advance pages, also
 * call {@see PdfPage::setDuration()}.
 *
 * Transition styles (use the class constants):
 *   REPLACE   — simply replace the page (default)
 *   SPLIT     — two lines sweep across the page
 *   BLINDS    — multiple lines sweep, like window blinds
 *   BOX       — a rectangular box expands or contracts
 *   WIPE      — a single sweeping line
 *   DISSOLVE  — old page dissolves to new one
 *   GLITTER   — glitter effect moving in a direction
 *   FLY       — new page flies in (PDF 1.5)
 *   PUSH      — old page pushed by new one (PDF 1.5)
 *   COVER     — new page slides to cover old one (PDF 1.5)
 *   UNCOVER   — old page slides to reveal new one (PDF 1.5)
 *   FADE      — new page gradually becomes visible (PDF 1.5)
 *
 * Usage:
 *   $page->setTransition(
 *       (new PageTransition(PageTransition::WIPE, 0.5))
 *           ->setDirection(270)          // sweep upward
 *   );
 *   $page->setDuration(5.0);             // advance after 5 s
 */
final class PageTransition
{
    // ── Transition styles ─────────────────────────────────────────────────────

    /** Simply replace the old page (no transition). */
    public const REPLACE  = 'R';
    /** Two lines sweep across the page, inward or outward. */
    public const SPLIT    = 'Split';
    /** Multiple parallel lines like window blinds. */
    public const BLINDS   = 'Blinds';
    /** A rectangular box expands (inward) or contracts (outward). */
    public const BOX      = 'Box';
    /** A single sweeping line moves across the page. */
    public const WIPE     = 'Wipe';
    /** The old page dissolves randomly into the new one. */
    public const DISSOLVE = 'Dissolve';
    /** Glitter effect sweeping in the specified direction. */
    public const GLITTER  = 'Glitter';
    /** The new page flies in (PDF 1.5). */
    public const FLY      = 'Fly';
    /** The new page pushes the old one out (PDF 1.5). */
    public const PUSH     = 'Push';
    /** The new page slides over to cover the old one (PDF 1.5). */
    public const COVER    = 'Cover';
    /** The old page slides away to uncover the new one (PDF 1.5). */
    public const UNCOVER  = 'Uncover';
    /** The new page gradually fades in (PDF 1.5). */
    public const FADE     = 'Fade';

    // ── Dimension ─────────────────────────────────────────────────────────────

    /** Horizontal split/blinds. */
    public const DIM_H = 'H';
    /** Vertical split/blinds. */
    public const DIM_V = 'V';

    // ── Motion ────────────────────────────────────────────────────────────────

    /** Box/Split sweeps inward (from edges to centre). */
    public const MOTION_IN  = 'I';
    /** Box/Split sweeps outward (from centre to edges). */
    public const MOTION_OUT = 'O';

    private PdfDictionary $dict;

    /**
     * @param string $style    Transition style — use one of the style constants.
     * @param float  $duration Duration of the transition effect in seconds.
     */
    public function __construct(string $style = self::REPLACE, float $duration = 1.0)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Trans'));
        $this->dict->set('S',    new PdfName($style));
        $this->dict->set('D',    new PdfReal($duration));
    }

    /**
     * Set the dimension (Split and Blinds only).
     *
     * @param string $dim  {@see self::DIM_H} or {@see self::DIM_V}.
     */
    public function setDimension(string $dim): static
    {
        $this->dict->set('Dm', new PdfName($dim));
        return $this;
    }

    /**
     * Set the motion direction (Box and Split only).
     *
     * @param string $motion  {@see self::MOTION_IN} or {@see self::MOTION_OUT}.
     */
    public function setMotion(string $motion): static
    {
        $this->dict->set('M', new PdfName($motion));
        return $this;
    }

    /**
     * Set the sweep direction in degrees (Wipe, Glitter, Fly, Cover, Uncover, Push).
     *
     * 0 = left to right, 90 = bottom to top, 180 = right to left, 270 = top to bottom.
     * Glitter also accepts 315 (top-left to bottom-right).
     */
    public function setDirection(int $degrees): static
    {
        $this->dict->set('Di', new PdfInteger($degrees));
        return $this;
    }

    /**
     * Set the starting or ending scale (Fly only).
     *
     * Values in the range [0, 1] control how much of the page is
     * covered by the flying page at the start (if motion is inward) or end.
     * Default: 1.0.
     */
    public function setStartingScale(float $scale): static
    {
        $this->dict->set('SS', new PdfReal($scale));
        return $this;
    }

    /**
     * Whether the area outside the flying page is opaque (Fly only).
     *
     * When false, the area outside shows the old page content.
     * When true, the area outside is opaque.  Default: false.
     */
    public function setOpaque(bool $opaque): static
    {
        $this->dict->set('B', new PdfBoolean($opaque));
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
