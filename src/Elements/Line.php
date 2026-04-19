<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Structure\PdfResources;

/**
 * Straight line segment element.
 *
 * Draws a single stroked line between two points.  Supports arbitrary
 * colours, line widths, and dash patterns.
 *
 *   Line::from(72, 700)->to(523, 700)
 *       ->color(Color::gray(0.7))
 *       ->width(0.5)
 *       ->dash([4, 2]);          // 4 pt on, 2 pt off
 *
 *   Line::between(72, 750, 523, 750)
 *       ->color(Color::hex('#0000cc'))
 *       ->width(1.5);
 *
 * Coordinates are in user-space points (lower-left origin).
 */
final class Line implements Element
{
    private float  $x2        = 0;
    private float  $y2        = 0;
    private Color  $color;
    private float  $lineWidth = 1.0;
    private array  $dash      = [];
    private float  $opacity   = 1.0;

    private function __construct(private readonly float $x1, private readonly float $y1)
    {
        $this->color = Color::black();
    }

    /**
     * Begin a line from (x1, y1).  Chain with {@see self::to()} to set the
     * endpoint.
     *
     * @param float $x1  Start X in points.
     * @param float $y1  Start Y in points.
     */
    public static function from(float $x1, float $y1): self
    {
        return new self($x1, $y1);
    }

    /**
     * Create a complete line in a single call.
     *
     * @param float $x1  Start X in points.
     * @param float $y1  Start Y in points.
     * @param float $x2  End X in points.
     * @param float $y2  End Y in points.
     */
    public static function between(float $x1, float $y1, float $x2, float $y2): self
    {
        $l     = new self($x1, $y1);
        $l->x2 = $x2;
        $l->y2 = $y2;
        return $l;
    }

    /**
     * Set the endpoint.
     *
     * @param float $x2  End X in points.
     * @param float $y2  End Y in points.
     */
    public function to(float $x2, float $y2): self
    {
        $this->x2 = $x2;
        $this->y2 = $y2;
        return $this;
    }

    /**
     * Set the stroke colour.
     *
     * @param Color $color  Stroke colour.
     */
    public function color(Color $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Set the stroke line width.
     *
     * @param float $w  Line width in points.
     */
    public function width(float $w): self
    {
        $this->lineWidth = $w;
        return $this;
    }

    /**
     * Apply a dash pattern to the stroke.
     *
     * @param float[] $pattern  Alternating on/off lengths in points.
     *                          Empty array (default) means a solid line.
     *                          Example: `[4, 2]` = 4 pt dash, 2 pt gap.
     */
    public function dash(array $pattern): self
    {
        $this->dash = $pattern;
        return $this;
    }

    /**
     * Set the stroke opacity.
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
        $this->color->applyStroke($cs);
        $cs->setLineWidth($this->lineWidth);
        if (!empty($this->dash)) {
            $cs->setDash($this->dash);
        }
        $cs->drawLine($this->x1, $this->y1, $this->x2, $this->y2);
        $cs->restore();
    }
}
