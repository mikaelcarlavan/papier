<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Structure\PdfResources;

/**
 * Filled and/or stroked rectangle element.
 *
 * Draws an axis-aligned rectangle.  Both fill and stroke are optional —
 * supply at least one for the element to be visible.
 *
 *   Rectangle::create(72, 700, 200, 50)
 *       ->fill(Color::hex('#e8f0fe'))
 *       ->stroke(Color::hex('#4a6fa5'), 1.5);
 *
 *   Rectangle::create(0, 780, 595, 61)
 *       ->fill(Color::rgb(0.2, 0.3, 0.6));
 *
 * Coordinates follow the standard PDF convention: `x` and `y` are the
 * lower-left corner; width and height extend right and upward.
 */
final class Rectangle implements Element
{
    private float  $x           = 0;
    private float  $y           = 0;
    private float  $w           = 100;
    private float  $h           = 50;
    private ?Color $fillColor   = null;
    private ?Color $strokeColor = null;
    private float  $lineWidth   = 1.0;
    private float  $opacity     = 1.0;

    private function __construct() {}

    /**
     * Create a rectangle with an initial position and size.
     *
     * @param float $x  Lower-left X position in points.
     * @param float $y  Lower-left Y position in points.
     * @param float $w  Width in points.
     * @param float $h  Height in points.
     */
    public static function create(
        float $x = 0, float $y = 0, float $w = 100, float $h = 50
    ): self {
        $r    = new self();
        $r->x = $x;
        $r->y = $y;
        $r->w = $w;
        $r->h = $h;
        return $r;
    }

    /**
     * Reposition the rectangle's lower-left corner.
     *
     * @param float $x  Horizontal position in points.
     * @param float $y  Vertical position in points.
     */
    public function at(float $x, float $y): self
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    /**
     * Set the dimensions of the rectangle.
     *
     * @param float $w  Width in points.
     * @param float $h  Height in points.
     */
    public function size(float $w, float $h): self
    {
        $this->w = $w;
        $this->h = $h;
        return $this;
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
     * Set the stroke (border) colour and line width.
     *
     * @param Color $color      Border colour.
     * @param float $lineWidth  Border line width in points (default 1.0).
     */
    public function stroke(Color $color, float $lineWidth = 1.0): self
    {
        $this->strokeColor = $color;
        $this->lineWidth   = $lineWidth;
        return $this;
    }

    /** Remove any fill colour (makes the rectangle transparent inside). */
    public function noFill(): self  { $this->fillColor  = null; return $this; }

    /** Remove any stroke colour (makes the rectangle borderless). */
    public function noStroke(): self { $this->strokeColor = null; return $this; }

    /**
     * Set the overall opacity of both fill and stroke.
     *
     * Implemented via a named ExtGState.
     *
     * @param float $opacity  0.0 = fully transparent, 1.0 = fully opaque.
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

        $cs->drawRect($this->x, $this->y, $this->w, $this->h, $hasFill, $hasStroke);
        $cs->restore();
    }
}
