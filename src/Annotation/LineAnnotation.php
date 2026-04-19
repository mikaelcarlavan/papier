<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Elements\Color;
use Papier\Objects\{PdfArray, PdfName, PdfReal};

/**
 * Line annotation with optional arrowheads (`/Subtype /Line`).
 *
 * The line runs from (`$lx1`, `$ly1`) to (`$lx2`, `$ly2`) inside the
 * bounding rectangle (`$x1`…`$y2`).  Call {@see self::finalize()} after
 * construction to write the `/L` array.
 *
 * Example:
 *
 *   $line = new LineAnnotation(70, 698, 302, 722,  72, 700, 300, 720);
 *   $line->finalize()
 *        ->setLineEndings('OpenArrow', 'None')
 *        ->setColor(1.0, 0.0, 0.0);
 */
final class LineAnnotation extends Annotation
{
    public function __construct(
        float $x1, float $y1, float $x2, float $y2,
        private float $lx1, private float $ly1,
        private float $lx2, private float $ly2,
    ) {
        parent::__construct($x1, $y1, $x2, $y2);
    }

    public function getSubtype(): string { return 'Line'; }

    /**
     * Write the `/L` line-endpoint array from the constructor coordinates.
     *
     * Must be called after construction to produce a valid annotation.
     */
    public function finalize(): static
    {
        $l = new PdfArray();
        $l->add(new PdfReal($this->lx1));
        $l->add(new PdfReal($this->ly1));
        $l->add(new PdfReal($this->lx2));
        $l->add(new PdfReal($this->ly2));
        $this->dict->set('L', $l);
        return $this;
    }

    /**
     * Set line-ending styles (`/LE`).
     *
     * @param string $start  Style for the first endpoint.
     * @param string $end    Style for the second endpoint.
     *                       Values: `None`, `Square`, `Circle`, `Diamond`,
     *                       `OpenArrow`, `ClosedArrow`, `Butt`,
     *                       `ROpenArrow`, `RClosedArrow`, `Slash`.
     */
    public function setLineEndings(string $start, string $end): static
    {
        $le = new PdfArray();
        $le->add(new PdfName($start));
        $le->add(new PdfName($end));
        $this->dict->set('LE', $le);
        return $this;
    }

    /**
     * Set the interior fill colour (`/IC`) for closed arrowheads.
     *
     * Only applies when a closed line-ending style (`ClosedArrow`,
     * `RClosedArrow`, `Diamond`, `Circle`, `Square`) is used.
     *
     * @param Color $color  Fill colour for the arrowhead interior.
     */
    public function setInteriorColor(Color $color): static
    {
        $this->dict->set('IC', $this->colorToArray($color));
        return $this;
    }
}
