<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Font\StandardFonts;
use Papier\Structure\PdfResources;

/**
 * Multi-line word-wrapped text element.
 *
 * Automatically breaks a string of text into lines that fit within a
 * specified bounding-box width, then renders them with a configurable
 * line height and horizontal alignment.  Blank lines (`\n\n`) start new
 * paragraphs.
 *
 *   TextBox::write('A long paragraph of text…')
 *       ->at(72, 700)
 *       ->size(400, 200)          // bounding box width × height in points
 *       ->font('F1', 11, 'Helvetica')
 *       ->lineHeight(1.4)         // 1.4 × font size
 *       ->align('justify')        // 'left' | 'center' | 'right'
 *       ->color(Color::black());
 *
 * Word-wrapping precision depends on font metrics.  When `$baseFontName`
 * matches one of the 14 built-in PDF fonts, AFM advance-width tables from
 * {@see \Papier\Font\StandardFonts} are used.  For other fonts a fallback
 * estimate of 0.55 × fontSize per character is used.
 *
 * If `$height` is 0, the box is unconstrained vertically and all lines are
 * rendered.  If it is positive, lines that exceed the box are silently
 * clipped.
 */
final class TextBox implements Element
{
    private float  $x            = 0;
    private float  $y            = 0;
    private float  $width        = 400;
    private float  $height       = 0;     // 0 = unconstrained
    private string $fontName     = 'F1';
    private string $baseFontName = '';    // for AFM metrics
    private float  $fontSize     = 12;
    private float  $lineHeight   = 1.2;  // multiplier of fontSize
    private string $align        = 'left';
    private Color  $color;

    private function __construct(private readonly string $text)
    {
        $this->color = Color::black();
    }

    /**
     * Create a text-box element.
     *
     * @param string $text  Text to display.  Newlines (`\n`) start new
     *                      paragraphs; all other whitespace is normalised.
     */
    public static function write(string $text): self
    {
        return new self($text);
    }

    /**
     * Set the top-left anchor of the bounding box.
     *
     * Note: `y` is the Y coordinate of the *first baseline*, not the top
     * edge of the box.  Lines descend from there.
     *
     * @param float $x  Left edge in points.
     * @param float $y  First baseline in points.
     */
    public function at(float $x, float $y): self
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    /**
     * Set the bounding-box dimensions.
     *
     * @param float $width   Maximum line width in points.
     * @param float $height  Maximum box height in points (0 = unconstrained).
     */
    public function size(float $width, float $height = 0): self
    {
        $this->width  = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Set the font resource name, size, and (optionally) the base font name
     * for AFM-based line-break metrics.
     *
     * @param string $fontName      Resource name returned by
     *                              {@see \Papier\PdfDocument::addFont()} (e.g. `'F1'`).
     * @param float  $size          Font size in points.
     * @param string $baseFontName  Optional built-in font name (e.g. `'Helvetica'`)
     *                              used for precise character-width metrics.
     *                              Without this, a rough 0.55 × size estimate is used.
     */
    public function font(string $fontName, float $size, string $baseFontName = ''): self
    {
        $this->fontName     = $fontName;
        $this->fontSize     = $size;
        $this->baseFontName = $baseFontName;
        return $this;
    }

    /**
     * Set the line spacing as a multiple of the font size.
     *
     * @param float $multiplier  1.0 = tightly packed, 1.2 = normal (default), 1.5 = loose.
     */
    public function lineHeight(float $multiplier): self
    {
        $this->lineHeight = $multiplier;
        return $this;
    }

    /**
     * Set horizontal text alignment within the bounding box.
     *
     * @param string $align  `'left'` | `'center'` | `'right'`.
     */
    public function align(string $align): self
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Set the text colour.
     *
     * @param Color $color  Any {@see Color} value.
     */
    public function color(Color $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Shorthand for setting an RGB colour.
     *
     * @param float $r Red   [0, 1].
     * @param float $g Green [0, 1].
     * @param float $b Blue  [0, 1].
     */
    public function rgb(float $r, float $g, float $b): self
    {
        $this->color = Color::rgb($r, $g, $b);
        return $this;
    }

    public function render(ContentStream $cs, PdfResources $resources): void
    {
        $leading  = $this->fontSize * $this->lineHeight;
        $lines    = $this->wrapText($this->text, $this->width);
        $maxLines = $this->height > 0
            ? (int) floor($this->height / $leading)
            : PHP_INT_MAX;
        $curY = $this->y;

        $cs->beginText();
        $this->color->applyFill($cs);
        $cs->setFont($this->fontName, $this->fontSize)
           ->setTextLeading($leading);

        foreach (array_slice($lines, 0, $maxLines) as $line) {
            $offsetX = $this->x;

            if ($this->align !== 'left' && $line !== '') {
                $lineW   = $this->measureWidth($line);
                $offsetX += match ($this->align) {
                    'right'  => $this->width - $lineW,
                    'center' => ($this->width - $lineW) / 2.0,
                    default  => 0,
                };
            }

            $cs->setTextPosition($offsetX, $curY)
               ->showText($line);
            $curY -= $leading;
        }

        $cs->endText();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Break a text string into lines that fit within $maxWidth points.
     *
     * @param  string  $text      Input text; newlines produce paragraph breaks.
     * @param  float   $maxWidth  Maximum line width in points.
     * @return string[]
     */
    private function wrapText(string $text, float $maxWidth): array
    {
        $lines = [];

        foreach (explode("\n", $text) as $para) {
            $words   = preg_split('/\s+/', trim($para)) ?: [];
            $current = '';

            foreach ($words as $word) {
                $candidate = $current === '' ? $word : "$current $word";
                if ($this->measureWidth($candidate) <= $maxWidth) {
                    $current = $candidate;
                } else {
                    if ($current !== '') {
                        $lines[] = $current;
                    }
                    $current = $word; // word wider than box: force its own line
                }
            }
            $lines[] = $current;
        }

        return $lines;
    }

    /**
     * Measure the display width of a string in points at the current font size.
     *
     * Uses AFM metrics for built-in fonts; falls back to a 0.55 × fontSize
     * per-character estimate for unknown fonts.
     *
     * @param  string $text  Text to measure.
     * @return float         Width in points.
     */
    private function measureWidth(string $text): float
    {
        if ($this->baseFontName !== '' && StandardFonts::isStandard($this->baseFontName)) {
            return StandardFonts::stringWidth($text, $this->baseFontName, $this->fontSize);
        }
        return strlen($text) * $this->fontSize * 0.55;
    }
}
