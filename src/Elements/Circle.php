<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Structure\PdfResources;

/**
 * Filled and/or stroked circle or ellipse element.
 *
 * Uses four cubic Bézier curves to approximate a circle to within 0.02 %
 * of the true radius (κ ≈ 0.5523 magic number).
 *
 *   // Perfect circle
 *   Circle::create(200, 400, 40)
 *       ->fill(Color::rgb(0.2, 0.5, 0.9))
 *       ->stroke(Color::black(), 1.5);
 *
 *   // Ellipse (rx ≠ ry)
 *   Circle::ellipse(200, 400, 60, 30)
 *       ->fill(Color::hex('#ff6b6b'));
 *
 * The centre coordinates (cx, cy) and radii (rx, ry) are in user-space
 * points.  The stroke is drawn outside the fill area.
 */
final class Circle implements Element
{
    private ?Color $fillColor   = null;
    private ?Color $strokeColor = null;
    private float  $lineWidth   = 1.0;
    private float  $opacity     = 1.0;

    private function __construct(
        private readonly float $cx,
        private readonly float $cy,
        private readonly float $rx,
        private readonly float $ry,
    ) {}

    /**
     * Create a perfect circle.
     *
     * @param float $cx      Centre X in points.
     * @param float $cy      Centre Y in points.
     * @param float $radius  Radius in points.
     */
    public static function create(float $cx, float $cy, float $radius): self
    {
        return new self($cx, $cy, $radius, $radius);
    }

    /**
     * Create an ellipse with independent horizontal and vertical radii.
     *
     * @param float $cx  Centre X in points.
     * @param float $cy  Centre Y in points.
     * @param float $rx  Horizontal radius in points.
     * @param float $ry  Vertical radius in points.
     */
    public static function ellipse(float $cx, float $cy, float $rx, float $ry): self
    {
        return new self($cx, $cy, $rx, $ry);
    }

    /**
     * Set the fill colour.
     *
     * @param Color $color  Interior fill colour.
     */
    public function fill(Color $color): self
    {
        $this->fillColor = $color;
        return $this;
    }

    /**
     * Set the stroke (outline) colour and line width.
     *
     * @param Color $color      Outline colour.
     * @param float $lineWidth  Line width in points (default 1.0).
     */
    public function stroke(Color $color, float $lineWidth = 1.0): self
    {
        $this->strokeColor = $color;
        $this->lineWidth   = $lineWidth;
        return $this;
    }

    /** Remove any fill colour. */
    public function noFill(): self   { $this->fillColor   = null; return $this; }

    /** Remove any stroke colour. */
    public function noStroke(): self { $this->strokeColor = null; return $this; }

    /**
     * Set the overall opacity of the shape.
     *
     * Implemented via a named ExtGState in the page resources.
     *
     * @param float $opacity  0.0 = transparent, 1.0 = opaque.
     */
    public function opacity(float $opacity): self
    {
        $this->opacity = max(0.0, min(1.0, $opacity));
        return $this;
    }

    public function render(ContentStream $cs, PdfResources $resources): void
    {
        $cs->save();
        if ($this->opacity < 1.0) {
            Text::registerOpacity($this->opacity, $cs, $resources);
        }

        $hasFill   = $this->fillColor   !== null;
        $hasStroke = $this->strokeColor !== null;

        if ($hasFill)   { $this->fillColor->applyFill($cs); }
        if ($hasStroke) {
            $this->strokeColor->applyStroke($cs);
            $cs->setLineWidth($this->lineWidth);
        }

        if ($this->rx === $this->ry) {
            $cs->drawCircle($this->cx, $this->cy, $this->rx);
        } else {
            $cs->drawEllipse($this->cx, $this->cy, $this->rx, $this->ry);
        }

        if ($hasFill && $hasStroke)   { $cs->fillStroke(); }
        elseif ($hasFill)             { $cs->fill(); }
        elseif ($hasStroke)           { $cs->stroke(); }

        $cs->restore();
    }
}
