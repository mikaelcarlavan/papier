<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Font\StandardFonts;
use Papier\Objects\{PdfDictionary, PdfName, PdfReal};
use Papier\Structure\PdfResources;

/**
 * Table element — draws a grid of rows and columns on a PDF page.
 *
 * The table anchor (`$x`, `$y`) is the **top-left corner** of the table.
 * Rows extend downward (decreasing PDF y) from there.
 *
 * Features:
 *   - Auto or fixed row heights
 *   - Colspan and rowspan
 *   - Horizontal and vertical text alignment per cell
 *   - Header rows and footer rows (separate font / bg / text colour)
 *   - Alternating row colours (zebra stripes)
 *   - Per-cell background, text colour, font, padding, border overrides
 *   - Outer border and inner grid lines independently configurable
 *   - Per-cell border side suppression
 *   - Opacity
 *   - Minimum row height constraint
 *   - Auto word-wrap with AFM metrics for built-in fonts
 *
 * Quick example:
 *
 *   $table = Table::create(36, 720)
 *       ->setColumnWidths(150, 80, 80, 80)
 *       ->setFont('F1', 10, 'Helvetica')
 *       ->setHeaderFont('F2', 10, 'Helvetica-Bold')
 *       ->setHeaderRows(1)
 *       ->setHeaderBg(Color::rgb(0.18, 0.33, 0.59))
 *       ->setHeaderTextColor(Color::white())
 *       ->setAltRowBg(Color::rgb(0.94, 0.96, 1.0))
 *       ->setBorder(Color::gray(0.4))
 *       ->setCellPadding(5);
 *
 *   $table->addRow(['Product',  'Q1', 'Q2', 'Q3']);
 *   $table->addRow(['Widgets', '1 200', '1 450', '1 680']);
 *   $page->add($table);
 *
 * Colspan:
 *
 *   $table->addRow([
 *       TableCell::make('Summary')->colspan(4)->align('center'),
 *   ]);
 *
 * Rowspan:
 *
 *   // Row 0: cell A spans 2 rows, then B, C
 *   $table->addRow([TableCell::make('A')->rowspan(2), 'B', 'C']);
 *   // Row 1: only D, E (column 0 is occupied by A's rowspan)
 *   $table->addRow(['D', 'E']);
 *
 * Per-cell overrides:
 *
 *   TableCell::make('Warn')
 *       ->bg(Color::rgb(1.0, 0.9, 0.8))
 *       ->color(Color::rgb(0.7, 0.3, 0.0))
 *       ->valign('middle')
 *       ->padding(8)
 *       ->borderSides(null, null, null, false); // hide left border
 */
final class Table implements Element
{
    // ── Position ──────────────────────────────────────────────────────────────

    private float $x = 72;
    private float $y = 700;

    // ── Data ──────────────────────────────────────────────────────────────────

    /** @var TableCell[][] */
    private array $rows = [];

    // ── Layout ────────────────────────────────────────────────────────────────

    /** @var float[] */
    private array $colWidths      = [];
    private float $tableWidth     = 451;
    private float $fixedRowHeight = 0;
    private float $minRowHeight   = 0;

    // Default cell padding
    private float $padTop   = 4;
    private float $padRight = 6;
    private float $padBot   = 4;
    private float $padLeft  = 6;

    // ── Default text style ────────────────────────────────────────────────────

    private string $fontName     = 'F1';
    private string $baseFontName = '';
    private float  $fontSize     = 10;
    private float  $lineHeight   = 1.3;
    private Color  $textColor;
    private string $textAlign    = 'left';
    private string $valign       = 'top';

    // ── Header style ──────────────────────────────────────────────────────────

    private int    $headerRows      = 0;
    private string $headerFontName  = '';
    private string $headerBaseName  = '';
    private float  $headerFontSize  = 0;
    private ?Color $headerBg        = null;
    private ?Color $headerTextColor = null;

    // ── Footer style ──────────────────────────────────────────────────────────

    private int    $footerRows      = 0;
    private string $footerFontName  = '';
    private string $footerBaseName  = '';
    private float  $footerFontSize  = 0;
    private ?Color $footerBg        = null;
    private ?Color $footerTextColor = null;

    // ── Row backgrounds ───────────────────────────────────────────────────────

    private ?Color $rowBg    = null;
    private ?Color $altRowBg = null;

    // ── Outer border ──────────────────────────────────────────────────────────

    private bool   $showBorder  = true;
    private ?Color $borderColor = null;
    private float  $borderWidth = 0.5;

    // ── Inner border ──────────────────────────────────────────────────────────

    private bool   $showInnerBorder  = true;
    private ?Color $innerBorderColor = null;
    private float  $innerBorderWidth = 0.5;

    // ── Opacity ───────────────────────────────────────────────────────────────

    private float $opacity = 1.0;

    // ─────────────────────────────────────────────────────────────────────────

    private function __construct()
    {
        $this->textColor   = Color::black();
        $this->borderColor = Color::gray(0.4);
    }

    // ── Factory ───────────────────────────────────────────────────────────────

    /**
     * Create a table anchored at the given top-left position.
     *
     * @param float $x  Left edge in points.
     * @param float $y  Top edge in points (PDF y-up coordinate system).
     */
    public static function create(float $x = 72, float $y = 700): self
    {
        $t    = new self();
        $t->x = $x;
        $t->y = $y;
        return $t;
    }

    // ── Data ──────────────────────────────────────────────────────────────────

    /**
     * Append a row of cells.
     *
     * Each element may be a plain `string` (table defaults apply) or a
     * {@see TableCell} (per-cell overrides apply).
     *
     * For rows that follow a rowspan from an earlier row, omit the cells
     * that are already covered by the span.
     *
     * @param (string|TableCell)[] $cells
     */
    public function addRow(array $cells): self
    {
        $normalized = [];
        foreach ($cells as $cell) {
            $normalized[] = ($cell instanceof TableCell)
                ? $cell
                : TableCell::make((string) $cell);
        }
        $this->rows[] = $normalized;
        return $this;
    }

    // ── Layout ────────────────────────────────────────────────────────────────

    /**
     * Set explicit column widths in points.
     *
     * The number of values determines the column count.  When set,
     * {@see self::setWidth()} is ignored.
     *
     * @param float ...$widths  One width per column.
     */
    public function setColumnWidths(float ...$widths): self
    {
        $this->colWidths = array_values($widths);
        return $this;
    }

    /**
     * Set the total table width; columns are distributed equally.
     *
     * Used only when no explicit column widths are given.
     *
     * @param float $width  Total width in points.
     */
    public function setWidth(float $width): self
    {
        $this->tableWidth = $width;
        return $this;
    }

    /**
     * Set a fixed row height for all rows.
     *
     * When `0` (the default), each row height is auto-computed from wrapped
     * text content.
     *
     * @param float $height  Row height in points.
     */
    public function setRowHeight(float $height): self
    {
        $this->fixedRowHeight = $height;
        return $this;
    }

    /**
     * Set a minimum row height floor for auto-height rows.
     *
     * Rows will never be shorter than this value, even if the cell content
     * fits in less space.
     *
     * @param float $height  Minimum row height in points.
     */
    public function setMinRowHeight(float $height): self
    {
        $this->minRowHeight = $height;
        return $this;
    }

    /**
     * Set uniform cell padding on all four sides.
     *
     * @param float $padding  Padding in points.
     */
    public function setCellPadding(float $padding): self
    {
        $this->padTop = $this->padRight = $this->padBot = $this->padLeft = $padding;
        return $this;
    }

    /**
     * Set horizontal and vertical cell padding separately.
     *
     * @param float $horizontal  Left and right padding.
     * @param float $vertical    Top and bottom padding.
     */
    public function setCellPaddingXY(float $horizontal, float $vertical): self
    {
        $this->padLeft = $this->padRight = $horizontal;
        $this->padTop  = $this->padBot   = $vertical;
        return $this;
    }

    /**
     * Set cell padding per side.
     *
     * @param float $top     Top padding.
     * @param float $right   Right padding.
     * @param float $bottom  Bottom padding.
     * @param float $left    Left padding.
     */
    public function setCellPaddingFull(float $top, float $right, float $bottom, float $left): self
    {
        $this->padTop   = $top;
        $this->padRight = $right;
        $this->padBot   = $bottom;
        $this->padLeft  = $left;
        return $this;
    }

    // ── Text style ────────────────────────────────────────────────────────────

    /**
     * Set the default font for all data cells.
     *
     * @param string $fontName      Resource name (e.g. `'F1'`).
     * @param float  $size          Font size in points.
     * @param string $baseFontName  Built-in font name for metrics (e.g. `'Helvetica'`).
     */
    public function setFont(string $fontName, float $size, string $baseFontName = ''): self
    {
        $this->fontName     = $fontName;
        $this->fontSize     = $size;
        $this->baseFontName = $baseFontName;
        return $this;
    }

    /**
     * Set the default text colour for all data cells.
     *
     * @param Color $color  Text colour.
     */
    public function setTextColor(Color $color): self
    {
        $this->textColor = $color;
        return $this;
    }

    /**
     * Set the default horizontal text alignment.
     *
     * @param string $align  `'left'` (default), `'center'`, or `'right'`.
     */
    public function setTextAlign(string $align): self
    {
        $this->textAlign = $align;
        return $this;
    }

    /**
     * Set the default vertical text alignment within cells.
     *
     * @param string $valign  `'top'` (default), `'middle'`, or `'bottom'`.
     */
    public function setVerticalAlign(string $valign): self
    {
        $this->valign = $valign;
        return $this;
    }

    /**
     * Set the default line-height multiplier.
     *
     * @param float $multiplier  Line height as a multiple of font size (default 1.3).
     */
    public function setLineHeight(float $multiplier): self
    {
        $this->lineHeight = $multiplier;
        return $this;
    }

    // ── Header style ──────────────────────────────────────────────────────────

    /**
     * Designate the first $count rows as header rows.
     *
     * @param int $count  Number of header rows at the top of the table.
     */
    public function setHeaderRows(int $count): self
    {
        $this->headerRows = max(0, $count);
        return $this;
    }

    /**
     * Set the font used for header rows.
     *
     * @param string $fontName      Resource name.
     * @param float  $size          Font size in points.
     * @param string $baseFontName  Built-in font name for metrics.
     */
    public function setHeaderFont(string $fontName, float $size, string $baseFontName = ''): self
    {
        $this->headerFontName = $fontName;
        $this->headerFontSize = $size;
        $this->headerBaseName = $baseFontName;
        return $this;
    }

    /**
     * Set the background colour of header rows.
     *
     * @param Color $color  Header background fill colour.
     */
    public function setHeaderBg(Color $color): self
    {
        $this->headerBg = $color;
        return $this;
    }

    /**
     * Set the text colour used in header rows.
     *
     * @param Color $color  Header text colour.
     */
    public function setHeaderTextColor(Color $color): self
    {
        $this->headerTextColor = $color;
        return $this;
    }

    // ── Footer style ──────────────────────────────────────────────────────────

    /**
     * Designate the last $count rows as footer rows.
     *
     * Footer rows receive distinct font, background, and text colour settings,
     * separate from both header rows and body rows.
     *
     * @param int $count  Number of footer rows at the bottom of the table.
     */
    public function setFooterRows(int $count): self
    {
        $this->footerRows = max(0, $count);
        return $this;
    }

    /**
     * Set the font used for footer rows.
     *
     * @param string $fontName      Resource name.
     * @param float  $size          Font size in points.
     * @param string $baseFontName  Built-in font name for metrics.
     */
    public function setFooterFont(string $fontName, float $size, string $baseFontName = ''): self
    {
        $this->footerFontName = $fontName;
        $this->footerFontSize = $size;
        $this->footerBaseName = $baseFontName;
        return $this;
    }

    /**
     * Set the background colour of footer rows.
     *
     * @param Color $color  Footer background fill colour.
     */
    public function setFooterBg(Color $color): self
    {
        $this->footerBg = $color;
        return $this;
    }

    /**
     * Set the text colour used in footer rows.
     *
     * @param Color $color  Footer text colour.
     */
    public function setFooterTextColor(Color $color): self
    {
        $this->footerTextColor = $color;
        return $this;
    }

    // ── Row backgrounds ───────────────────────────────────────────────────────

    /**
     * Set the default background fill for body rows.
     *
     * @param Color|null $color  Background colour, or null for transparent.
     */
    public function setRowBg(?Color $color): self
    {
        $this->rowBg = $color;
        return $this;
    }

    /**
     * Set the alternating background colour for even-numbered body rows (zebra stripes).
     *
     * @param Color $color  Alternate row background colour.
     */
    public function setAltRowBg(Color $color): self
    {
        $this->altRowBg = $color;
        return $this;
    }

    // ── Borders ───────────────────────────────────────────────────────────────

    /**
     * Configure the outer border of the table.
     *
     * @param Color $color  Border colour.
     * @param float $width  Line width in points (default 0.5).
     */
    public function setBorder(Color $color, float $width = 0.5): self
    {
        $this->showBorder  = true;
        $this->borderColor = $color;
        $this->borderWidth = $width;
        return $this;
    }

    /**
     * Configure the inner grid lines (between cells).
     *
     * When not called, inner borders inherit the outer border's colour and
     * width.
     *
     * @param Color $color  Grid line colour.
     * @param float $width  Line width in points.
     */
    public function setInnerBorder(Color $color, float $width = 0.5): self
    {
        $this->showInnerBorder  = true;
        $this->innerBorderColor = $color;
        $this->innerBorderWidth = $width;
        return $this;
    }

    /** Remove the outer table border. */
    public function noBorder(): self
    {
        $this->showBorder = false;
        return $this;
    }

    /** Remove all inner grid lines. */
    public function noInnerBorder(): self
    {
        $this->showInnerBorder = false;
        return $this;
    }

    // ── Opacity ───────────────────────────────────────────────────────────────

    /**
     * Set the overall opacity of the table (backgrounds, borders, and text).
     *
     * Implemented via a named ExtGState resource.
     *
     * @param float $opacity  0.0 = fully transparent, 1.0 = fully opaque.
     */
    public function opacity(float $opacity): self
    {
        $this->opacity = max(0.0, min(1.0, $opacity));
        return $this;
    }

    // ── Rendering ─────────────────────────────────────────────────────────────

    public function render(ContentStream $cs, PdfResources $resources): void
    {
        if (empty($this->rows)) {
            return;
        }

        $numCols = $this->computeColumnCount();
        $colW    = $this->resolveColumnWidths($numCols);

        // Build the occupancy grid for rowspan support
        [$grid, $owner] = $this->buildGrid($numCols);

        $rowH   = $this->computeRowHeights($colW, $grid);
        $totalW = array_sum($colW);
        $totalH = array_sum($rowH);

        $cs->save();

        if ($this->opacity < 1.0) {
            Text::registerOpacity($this->opacity, $cs, $resources);
        }

        $this->renderBackgrounds($cs, $colW, $rowH, $grid, $totalW);
        $this->renderText($cs, $resources, $colW, $rowH, $grid);
        $this->renderBorders($cs, $colW, $rowH, $grid, $owner, $numCols, $totalW, $totalH);

        $cs->restore();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Grid construction
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a 2D occupancy grid from `$this->rows`, handling colspan and rowspan.
     *
     * Returns:
     *   $grid[$r][$c]  = TableCell  — only set for the top-left cell of each span
     *   $owner[$r][$c] = [$ownerR, $ownerC]  — for every position in a span
     *
     * @return array{array<int, array<int, TableCell>>, array<int, array<int, array{int,int}>>}
     */
    private function buildGrid(int $numCols): array
    {
        $grid  = [];
        $owner = [];

        foreach ($this->rows as $r => $row) {
            $c = 0;
            foreach ($row as $cell) {
                // Skip columns occupied by rowspans from above
                while ($c < $numCols && isset($owner[$r][$c])) {
                    $c++;
                }
                if ($c >= $numCols) {
                    break;
                }

                // Clamp span to available columns/rows
                $cs2 = min($cell->colspan, $numCols - $c);
                $rs  = $cell->rowspan;

                $cell->gridRow = $r;
                $cell->gridCol = $c;
                $grid[$r][$c]  = $cell;

                for ($dr = 0; $dr < $rs; $dr++) {
                    for ($dc = 0; $dc < $cs2; $dc++) {
                        $owner[$r + $dr][$c + $dc] = [$r, $c];
                    }
                }

                $c += $cs2;
            }
        }

        return [$grid, $owner];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Layout helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function computeColumnCount(): int
    {
        if (!empty($this->colWidths)) {
            return count($this->colWidths);
        }
        $max = 1;
        foreach ($this->rows as $row) {
            $cols = 0;
            foreach ($row as $cell) {
                $cols += $cell->colspan;
            }
            $max = max($max, $cols);
        }
        return $max;
    }

    /**
     * @return float[]
     */
    private function resolveColumnWidths(int $numCols): array
    {
        if (!empty($this->colWidths)) {
            $w = $this->colWidths;
            while (count($w) < $numCols) { $w[] = 60; }
            return array_slice($w, 0, $numCols);
        }
        $each = $this->tableWidth / max(1, $numCols);
        return array_fill(0, $numCols, $each);
    }

    /**
     * Compute row heights from cell content.
     *
     * Strategy for rowspan > 1 cells:
     *   1. Compute natural heights from non-spanning cells only.
     *   2. For each rowspan > 1 cell, if its natural height exceeds the sum
     *      of covered rows, distribute the excess evenly to those rows.
     *
     * @param  float[]   $colW
     * @param  array<int, array<int, TableCell>> $grid
     * @return float[]
     */
    private function computeRowHeights(array $colW, array $grid): array
    {
        $numRows = count($this->rows);
        $rowH    = array_fill(0, $numRows, 0.0);

        if ($this->fixedRowHeight > 0) {
            return array_fill(0, $numRows, (float) $this->fixedRowHeight);
        }

        // Pass 1: cells with rowspan == 1
        foreach ($grid as $r => $rowCells) {
            foreach ($rowCells as $c => $cell) {
                if ($cell->rowspan !== 1) {
                    continue;
                }
                $natural = $this->naturalCellHeight($cell, $colW, $r);
                $rowH[$r] = max($rowH[$r], $natural);
            }
        }

        // Apply minimum row height
        $floor = $this->minRowHeight;
        foreach ($rowH as &$h) {
            $h = max($h, $floor, $this->padTop + $this->fontSize + $this->padBot);
        }
        unset($h);

        // Pass 2: distribute excess for rowspan > 1 cells
        foreach ($grid as $r => $rowCells) {
            foreach ($rowCells as $c => $cell) {
                if ($cell->rowspan <= 1) {
                    continue;
                }
                $natural  = $this->naturalCellHeight($cell, $colW, $r);
                $spanH    = 0.0;
                for ($dr = 0; $dr < $cell->rowspan && ($r + $dr) < $numRows; $dr++) {
                    $spanH += $rowH[$r + $dr];
                }
                if ($natural > $spanH) {
                    $extra = ($natural - $spanH) / $cell->rowspan;
                    for ($dr = 0; $dr < $cell->rowspan && ($r + $dr) < $numRows; $dr++) {
                        $rowH[$r + $dr] += $extra;
                    }
                }
            }
        }

        return $rowH;
    }

    /** Compute the natural height of a single cell (text + padding). */
    private function naturalCellHeight(TableCell $cell, array $colW, int $r): float
    {
        $rowType        = $this->rowType($r);
        [$fName, $fSize, $fBase, $lh] = $this->resolveCellFont($cell, $rowType);
        [$pT, $pR, $pB, $pL]          = $this->resolveCellPadding($cell);
        $cellW  = $this->spanWidth($colW, $cell->gridCol, $cell->colspan);
        $innerW = $cellW - $pL - $pR;
        $lines  = $this->wrapText($cell->text, max(1, $innerW), $fBase, $fSize);
        $leading = $fSize * $lh;
        return $pT + count($lines) * $leading + $pB;
    }

    /** Sum widths of $count columns starting at $startCol. */
    private function spanWidth(array $colW, int $startCol, int $colspan): float
    {
        $w = 0;
        for ($c = $startCol; $c < $startCol + $colspan && $c < count($colW); $c++) {
            $w += $colW[$c];
        }
        return $w;
    }

    /** Sum heights of $count rows starting at $startRow. */
    private function spanHeight(array $rowH, int $startRow, int $rowspan): float
    {
        $h = 0;
        for ($r = $startRow; $r < $startRow + $rowspan && $r < count($rowH); $r++) {
            $h += $rowH[$r];
        }
        return $h;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Style resolution helpers
    // ─────────────────────────────────────────────────────────────────────────

    private const ROW_HEADER = 'header';
    private const ROW_FOOTER = 'footer';
    private const ROW_BODY   = 'body';

    private function rowType(int $r): string
    {
        if ($r < $this->headerRows) {
            return self::ROW_HEADER;
        }
        if ($r >= count($this->rows) - $this->footerRows && $this->footerRows > 0) {
            return self::ROW_FOOTER;
        }
        return self::ROW_BODY;
    }

    /**
     * Resolve effective font for a cell.
     *
     * @return array{string, float, string, float}  [fontName, fontSize, baseFontName, lineHeight]
     */
    private function resolveCellFont(TableCell $cell, string $rowType): array
    {
        if ($cell->fontName !== '') {
            $lh = $cell->lineHeight > 0 ? $cell->lineHeight : $this->lineHeight;
            return [$cell->fontName, $cell->fontSize ?: $this->fontSize, $cell->baseFontName, $lh];
        }
        [$fn, $fs, $fb] = match ($rowType) {
            self::ROW_HEADER => $this->headerFontName !== ''
                ? [$this->headerFontName, $this->headerFontSize ?: $this->fontSize, $this->headerBaseName]
                : [$this->fontName, $this->fontSize, $this->baseFontName],
            self::ROW_FOOTER => $this->footerFontName !== ''
                ? [$this->footerFontName, $this->footerFontSize ?: $this->fontSize, $this->footerBaseName]
                : [$this->fontName, $this->fontSize, $this->baseFontName],
            default => [$this->fontName, $this->fontSize, $this->baseFontName],
        };
        $lh = $cell->lineHeight > 0 ? $cell->lineHeight : $this->lineHeight;
        return [$fn, $fs, $fb, $lh];
    }

    /**
     * Resolve effective padding for a cell.
     *
     * @return float[]  [top, right, bottom, left, right] — [pT, pR, pB, pL, pR]
     * Actually returns [pT, pR, pB, pL, pR] — index 0=top, 1=right, 2=bottom, 3=left
     */
    private function resolveCellPadding(TableCell $cell): array
    {
        return [
            $cell->padTop   >= 0 ? $cell->padTop   : $this->padTop,
            $cell->padRight >= 0 ? $cell->padRight  : $this->padRight,
            $cell->padBot   >= 0 ? $cell->padBot    : $this->padBot,
            $cell->padLeft  >= 0 ? $cell->padLeft   : $this->padLeft,
        ];
    }

    /** Resolve the row background colour. */
    private function resolveRowBg(string $rowType, int $bodyIdx): ?Color
    {
        return match ($rowType) {
            self::ROW_HEADER => $this->headerBg,
            self::ROW_FOOTER => $this->footerBg,
            default => $this->altRowBg !== null
                ? (($bodyIdx % 2 === 1) ? $this->altRowBg : $this->rowBg)
                : $this->rowBg,
        };
    }

    /** Compute the X position of the left edge of column $c. */
    private function colX(array $colW, int $c): float
    {
        return $this->x + array_sum(array_slice($colW, 0, $c));
    }

    /** Compute the Y position of the top edge of row $r (in PDF y-up coords). */
    private function rowY(array $rowH, int $r): float
    {
        return $this->y - array_sum(array_slice($rowH, 0, $r));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Rendering passes
    // ─────────────────────────────────────────────────────────────────────────

    private function renderBackgrounds(
        ContentStream $cs,
        array $colW, array $rowH,
        array $grid,
        float $totalW,
    ): void {
        $numRows = count($this->rows);
        $bodyIdx = 0;

        // Draw row-level backgrounds (full row width)
        for ($r = 0; $r < $numRows; $r++) {
            $rowType = $this->rowType($r);
            $rowBg   = $this->resolveRowBg($rowType, $bodyIdx);
            if ($rowType === self::ROW_BODY) {
                $bodyIdx++;
            }
            if ($rowBg !== null) {
                $cs->save();
                $rowBg->applyFill($cs);
                $cs->rectangle($this->x, $this->rowY($rowH, $r) - $rowH[$r], $totalW, $rowH[$r])
                   ->fill();
                $cs->restore();
            }
        }

        // Draw per-cell background overrides (may span multiple rows/cols)
        foreach ($grid as $r => $rowCells) {
            foreach ($rowCells as $c => $cell) {
                if ($cell->bgColor === null) {
                    continue;
                }
                $cx = $this->colX($colW, $c);
                $cy = $this->rowY($rowH, $r);
                $cw = $this->spanWidth($colW, $c, $cell->colspan);
                $ch = $this->spanHeight($rowH, $r, $cell->rowspan);
                $cs->save();
                $cell->bgColor->applyFill($cs);
                $cs->rectangle($cx, $cy - $ch, $cw, $ch)->fill();
                $cs->restore();
            }
        }
    }

    private function renderText(
        ContentStream $cs,
        PdfResources  $resources,
        array $colW, array $rowH,
        array $grid,
    ): void {
        foreach ($grid as $r => $rowCells) {
            $rowType = $this->rowType($r);
            foreach ($rowCells as $c => $cell) {
                $cx = $this->colX($colW, $c);
                $cy = $this->rowY($rowH, $r);
                $cw = $this->spanWidth($colW, $c, $cell->colspan);
                $ch = $this->spanHeight($rowH, $r, $cell->rowspan);
                $this->renderCellText($cs, $resources, $cell, $rowType, $cx, $cy, $cw, $ch);
            }
        }
    }

    private function renderCellText(
        ContentStream $cs,
        PdfResources  $resources,
        TableCell     $cell,
        string        $rowType,
        float $cx, float $cy,
        float $cw, float $ch,
    ): void {
        if ($cell->text === '') {
            return;
        }

        [$fName, $fSize, $fBase, $lhMul] = $this->resolveCellFont($cell, $rowType);
        [$pT, $pR, $pB, $pL]            = $this->resolveCellPadding($cell);

        $textColor = $cell->textColor
            ?? match ($rowType) {
                self::ROW_HEADER => $this->headerTextColor,
                self::ROW_FOOTER => $this->footerTextColor,
                default          => null,
            }
            ?? $this->textColor;

        $align  = $cell->align  !== '' ? $cell->align  : $this->textAlign;
        $valign = $cell->valign !== '' ? $cell->valign : $this->valign;

        $innerW  = max(1, $cw - $pL - $pR);
        $leading = $fSize * $lhMul;
        $lines   = $this->wrapText($cell->text, $innerW, $fBase, $fSize);

        // Vertical alignment: compute Y of first baseline
        $nLines      = count($lines);
        $blockHeight = ($nLines - 1) * $leading + $fSize; // cap-height approx
        $innerH      = $ch - $pT - $pB;

        $topOffset = match ($valign) {
            'bottom' => $innerH - $blockHeight,
            'middle' => ($innerH - $blockHeight) / 2.0,
            default  => 0.0, // top
        };
        $topOffset = max(0.0, $topOffset);

        $lineY = $cy - $pT - $topOffset - $fSize;

        // Clip to cell bounds
        $cs->save();
        $cs->rectangle($cx, $cy - $ch, $cw, $ch)->clip()->endPath();

        $cs->beginText();
        $textColor->applyFill($cs);
        $cs->setFont($fName, $fSize);

        $bottomLimit = $cy - $ch + $pB;

        foreach ($lines as $line) {
            if ($lineY < $bottomLimit) {
                break;
            }
            $tx = $cx + $pL;
            if ($align !== 'left' && $line !== '') {
                $lineW = $this->measureWidth($line, $fBase, $fSize);
                $tx   += match ($align) {
                    'right'  => $innerW - $lineW,
                    'center' => ($innerW - $lineW) / 2.0,
                    default  => 0.0,
                };
            }
            $cs->setTextPosition($tx, $lineY)->showText($line);
            $lineY -= $leading;
        }

        $cs->endText();
        $cs->restore();
    }

    /**
     * Draw borders using a per-cell approach that correctly handles rowspan and
     * colspan.  Each cell is responsible for drawing its own 4 sides; adjacent
     * cells drawing the same line at the same position simply overdraw.
     *
     * Border visibility (from highest to lowest priority):
     *   1. Per-cell $cell->borderTop/Right/Bottom/Left  (null = inherit)
     *   2. Outer border settings for edges on the table perimeter
     *   3. Inner border settings for interior lines
     *   4. showBorder / showInnerBorder flags
     */
    private function renderBorders(
        ContentStream $cs,
        array $colW, array $rowH,
        array $grid,
        array $owner,
        int   $numCols,
        float $totalW,
        float $totalH,
    ): void {
        $numRows     = count($this->rows);
        $outerColor  = $this->borderColor ?? Color::gray(0.4);
        $innerColor  = $this->innerBorderColor ?? $outerColor;
        $outerW      = $this->borderWidth;
        $innerW      = $this->innerBorderWidth;

        $cs->save();

        foreach ($grid as $r => $rowCells) {
            foreach ($rowCells as $c => $cell) {
                $cx = $this->colX($colW, $c);
                $cy = $this->rowY($rowH, $r);
                $cw = $this->spanWidth($colW, $c, $cell->colspan);
                $ch = $this->spanHeight($rowH, $r, $cell->rowspan);
                $rs = min($cell->rowspan, $numRows - $r);
                $cs2 = min($cell->colspan, $numCols - $c);

                $isTopEdge    = ($r === 0);
                $isBottomEdge = ($r + $rs >= $numRows);
                $isLeftEdge   = ($c === 0);
                $isRightEdge  = ($c + $cs2 >= $numCols);

                // ── Top side ────────────────────────────────────────────────
                $this->drawSide(
                    $cs, $cell->borderTop,
                    $isTopEdge ? $this->showBorder : $this->showInnerBorder,
                    $this->resolveBorderStyle($cell, $isTopEdge, $outerColor, $innerColor, $outerW, $innerW),
                    $cx, $cy, $cx + $cw, $cy,
                );

                // ── Bottom side ──────────────────────────────────────────────
                $this->drawSide(
                    $cs, $cell->borderBottom,
                    $isBottomEdge ? $this->showBorder : $this->showInnerBorder,
                    $this->resolveBorderStyle($cell, $isBottomEdge, $outerColor, $innerColor, $outerW, $innerW),
                    $cx, $cy - $ch, $cx + $cw, $cy - $ch,
                );

                // ── Left side ────────────────────────────────────────────────
                $this->drawSide(
                    $cs, $cell->borderLeft,
                    $isLeftEdge ? $this->showBorder : $this->showInnerBorder,
                    $this->resolveBorderStyle($cell, $isLeftEdge, $outerColor, $innerColor, $outerW, $innerW),
                    $cx, $cy, $cx, $cy - $ch,
                );

                // ── Right side ───────────────────────────────────────────────
                $this->drawSide(
                    $cs, $cell->borderRight,
                    $isRightEdge ? $this->showBorder : $this->showInnerBorder,
                    $this->resolveBorderStyle($cell, $isRightEdge, $outerColor, $innerColor, $outerW, $innerW),
                    $cx + $cw, $cy, $cx + $cw, $cy - $ch,
                );
            }
        }

        $cs->restore();
    }

    /**
     * Draw one border side, honouring the cell-level override and show flag.
     *
     * @param array{Color, float} $style  [colour, width]
     */
    private function drawSide(
        ContentStream $cs,
        ?bool         $cellOverride,
        bool          $showDefault,
        array         $style,
        float $x1, float $y1, float $x2, float $y2,
    ): void {
        $show = $cellOverride ?? $showDefault;
        if (!$show) {
            return;
        }
        [$color, $width] = $style;
        $cs->save();
        $color->applyStroke($cs);
        $cs->setLineWidth($width)
           ->moveTo($x1, $y1)
           ->lineTo($x2, $y2)
           ->stroke();
        $cs->restore();
    }

    /**
     * Resolve [Color, width] for a border side, choosing outer vs inner settings.
     *
     * Per-cell $cell->borderColor and $cell->borderWidth take priority if set.
     *
     * @return array{Color, float}
     */
    private function resolveBorderStyle(
        TableCell $cell,
        bool      $isEdge,
        Color     $outerColor,
        Color     $innerColor,
        float     $outerW,
        float     $innerW,
    ): array {
        $color = $cell->borderColor ?? ($isEdge ? $outerColor : $innerColor);
        $width = $cell->borderWidth >= 0 ? $cell->borderWidth : ($isEdge ? $outerW : $innerW);
        return [$color, $width];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Text utilities
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @return string[]
     */
    private function wrapText(string $text, float $maxWidth, string $baseFontName, float $size): array
    {
        $lines = [];
        foreach (explode("\n", $text) as $para) {
            $words = preg_split('/\s+/', trim($para)) ?: [];
            if ($words === ['']) {
                $lines[] = '';
                continue;
            }
            $current = '';
            foreach ($words as $word) {
                $candidate = $current === '' ? $word : "$current $word";
                if ($this->measureWidth($candidate, $baseFontName, $size) <= $maxWidth) {
                    $current = $candidate;
                } else {
                    if ($current !== '') {
                        $lines[] = $current;
                    }
                    $current = $word;
                }
            }
            if ($current !== '') {
                $lines[] = $current;
            }
        }
        return $lines ?: [''];
    }

    private function measureWidth(string $text, string $baseFontName, float $size): float
    {
        if ($baseFontName !== '' && StandardFonts::isStandard($baseFontName)) {
            return StandardFonts::stringWidth($text, $baseFontName, $size);
        }
        return strlen($text) * $size * 0.55;
    }
}
