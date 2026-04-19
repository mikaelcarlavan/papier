<?php

declare(strict_types=1);

namespace Papier\Elements;

/**
 * A single cell in a {@see Table}.
 *
 * Carries the cell's text content plus optional per-cell style overrides.
 * Every property that is `null` / `''` / `0` falls back to the table-level
 * default.  Construct with {@see self::make()} and chain setters.
 *
 * Example:
 *
 *   TableCell::make('Revenue')
 *       ->colspan(2)
 *       ->rowspan(2)
 *       ->valign('middle')
 *       ->align('center')
 *       ->bg(Color::rgb(0.2, 0.4, 0.7))
 *       ->color(Color::white())
 *       ->font('F2', 11)
 *       ->padding(8)
 *       ->border(Color::rgb(0.5, 0.5, 0.5), 1.0);
 */
final class TableCell
{
    /** Visible text content of this cell. */
    public string $text = '';

    // ── Span ──────────────────────────────────────────────────────────────────

    /** Number of columns this cell spans (default 1). */
    public int $colspan = 1;

    /** Number of rows this cell spans (default 1). */
    public int $rowspan = 1;

    // ── Text style ────────────────────────────────────────────────────────────

    /**
     * Horizontal alignment: `'left'`, `'center'`, `'right'`, or `''` to
     * inherit the table default.
     */
    public string $align = '';

    /**
     * Vertical alignment: `'top'`, `'middle'`, `'bottom'`, or `''` to
     * inherit the table default.
     */
    public string $valign = '';

    /**
     * Font resource name (e.g. `'F2'`), or `''` to inherit the table default.
     */
    public string $fontName = '';

    /** Font size in points, or `0` to inherit. */
    public float $fontSize = 0;

    /**
     * Base font name for metrics (e.g. `'Helvetica-Bold'`), or `''` to
     * inherit.
     */
    public string $baseFontName = '';

    /**
     * Line-height multiplier (e.g. 1.4), or `0` to inherit the table default.
     */
    public float $lineHeight = 0;

    // ── Colours ───────────────────────────────────────────────────────────────

    /** Per-cell background fill colour, or null to inherit. */
    public ?Color $bgColor = null;

    /** Per-cell text colour, or null to inherit. */
    public ?Color $textColor = null;

    // ── Padding ───────────────────────────────────────────────────────────────

    /** Per-cell top padding in points, or -1 to inherit. */
    public float $padTop = -1;

    /** Per-cell right padding in points, or -1 to inherit. */
    public float $padRight = -1;

    /** Per-cell bottom padding in points, or -1 to inherit. */
    public float $padBot = -1;

    /** Per-cell left padding in points, or -1 to inherit. */
    public float $padLeft = -1;

    // ── Borders ───────────────────────────────────────────────────────────────

    /** Override border colour for all four sides of this cell, or null to inherit. */
    public ?Color $borderColor = null;

    /** Override border width for all four sides of this cell, or -1 to inherit. */
    public float $borderWidth = -1;

    /**
     * Show/hide individual sides.  null = inherit the table's border settings.
     * true = force show, false = force hide.
     */
    public ?bool $borderTop    = null;
    public ?bool $borderRight  = null;
    public ?bool $borderBottom = null;
    public ?bool $borderLeft   = null;

    // ── Internal grid position (set by Table during layout) ───────────────────

    /** @internal */
    public int $gridRow = 0;
    /** @internal */
    public int $gridCol = 0;

    // ─────────────────────────────────────────────────────────────────────────

    private function __construct() {}

    /**
     * Create a cell with the given text content.
     *
     * @param string $text  Cell text (UTF-8).  Newlines produce paragraph breaks.
     */
    public static function make(string $text): self
    {
        $c       = new self();
        $c->text = $text;
        return $c;
    }

    // ── Span ──────────────────────────────────────────────────────────────────

    /**
     * Span this cell across multiple columns.
     *
     * @param int $n  Number of columns to span (minimum 1).
     */
    public function colspan(int $n): self
    {
        $this->colspan = max(1, $n);
        return $this;
    }

    /**
     * Span this cell across multiple rows.
     *
     * The cell occupies rows `r` through `r + n - 1`.  Cells in subsequent
     * rows must not include a cell for the columns covered by this span.
     *
     * @param int $n  Number of rows to span (minimum 1).
     */
    public function rowspan(int $n): self
    {
        $this->rowspan = max(1, $n);
        return $this;
    }

    // ── Text style ────────────────────────────────────────────────────────────

    /**
     * Set horizontal text alignment.
     *
     * @param string $align  `'left'`, `'center'`, or `'right'`.
     */
    public function align(string $align): self
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Set vertical text alignment within the cell.
     *
     * @param string $valign  `'top'`, `'middle'`, or `'bottom'`.
     */
    public function valign(string $valign): self
    {
        $this->valign = $valign;
        return $this;
    }

    /**
     * Set the font resource name and size for this cell.
     *
     * @param string $fontName      Resource name (e.g. `'F2'`).
     * @param float  $size          Font size in points.
     * @param string $baseFontName  Optional built-in font name for metrics
     *                              (e.g. `'Helvetica-Bold'`).
     */
    public function font(string $fontName, float $size, string $baseFontName = ''): self
    {
        $this->fontName     = $fontName;
        $this->fontSize     = $size;
        $this->baseFontName = $baseFontName;
        return $this;
    }

    /**
     * Set the line-height multiplier for wrapped text in this cell.
     *
     * @param float $multiplier  e.g. `1.3` (default) or `1.5` for loose lines.
     */
    public function lineHeight(float $multiplier): self
    {
        $this->lineHeight = $multiplier;
        return $this;
    }

    // ── Colours ───────────────────────────────────────────────────────────────

    /**
     * Set the cell background colour.
     *
     * @param Color $color  Fill colour.
     */
    public function bg(Color $color): self
    {
        $this->bgColor = $color;
        return $this;
    }

    /**
     * Set the cell text colour.
     *
     * @param Color $color  Text colour.
     */
    public function color(Color $color): self
    {
        $this->textColor = $color;
        return $this;
    }

    // ── Padding ───────────────────────────────────────────────────────────────

    /**
     * Set uniform padding on all four sides of this cell.
     *
     * @param float $padding  Padding in points.
     */
    public function padding(float $padding): self
    {
        $this->padTop = $this->padRight = $this->padBot = $this->padLeft = $padding;
        return $this;
    }

    /**
     * Set horizontal and vertical padding separately.
     *
     * @param float $horizontal  Left and right padding in points.
     * @param float $vertical    Top and bottom padding in points.
     */
    public function paddingXY(float $horizontal, float $vertical): self
    {
        $this->padLeft = $this->padRight = $horizontal;
        $this->padTop  = $this->padBot   = $vertical;
        return $this;
    }

    /**
     * Set padding per side.
     *
     * @param float $top     Top padding in points.
     * @param float $right   Right padding in points.
     * @param float $bottom  Bottom padding in points.
     * @param float $left    Left padding in points.
     */
    public function paddingFull(float $top, float $right, float $bottom, float $left): self
    {
        $this->padTop   = $top;
        $this->padRight = $right;
        $this->padBot   = $bottom;
        $this->padLeft  = $left;
        return $this;
    }

    // ── Borders ───────────────────────────────────────────────────────────────

    /**
     * Override the border colour and width for all sides of this cell.
     *
     * @param Color $color  Border colour.
     * @param float $width  Border width in points (-1 = inherit).
     */
    public function border(Color $color, float $width = -1): self
    {
        $this->borderColor = $color;
        $this->borderWidth = $width;
        return $this;
    }

    /**
     * Show or hide individual cell border sides.
     *
     * Pass `true` to force a side visible, `false` to suppress it, or
     * `null` to inherit the table-level behaviour.
     *
     * @param bool|null $top     Top side.
     * @param bool|null $right   Right side.
     * @param bool|null $bottom  Bottom side.
     * @param bool|null $left    Left side.
     */
    public function borderSides(?bool $top, ?bool $right, ?bool $bottom, ?bool $left): self
    {
        $this->borderTop    = $top;
        $this->borderRight  = $right;
        $this->borderBottom = $bottom;
        $this->borderLeft   = $left;
        return $this;
    }

    /**
     * Suppress all four border sides of this cell (no border drawn at all).
     *
     * Useful for invisible spacer cells.
     */
    public function noBorder(): self
    {
        $this->borderTop = $this->borderRight = $this->borderBottom = $this->borderLeft = false;
        return $this;
    }
}
