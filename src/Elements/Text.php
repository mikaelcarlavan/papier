<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Objects\{PdfDictionary, PdfName, PdfReal};
use Papier\Structure\PdfResources;

/**
 * Single-line text element.
 *
 * Renders a single string of text at a fixed position using a registered
 * font.  For automatic word-wrapping across multiple lines, use
 * {@see TextBox} instead.
 *
 *   Text::write('Hello, World!')
 *       ->at(72, 720)
 *       ->font($fontName, 24)
 *       ->color(Color::hex('#1a1a1a'));
 *
 * The `$fontName` parameter must be a resource name returned by
 * {@see \Papier\PdfDocument::addFont()} (e.g. `'F1'`).
 *
 * Coordinates are in user-space points with the origin at the lower-left
 * corner of the page (default PDF coordinate system).
 */
final class Text implements Element
{
    private float  $x        = 0;
    private float  $y        = 0;
    private string $fontName = 'F1';
    private float  $fontSize = 12;
    private Color  $color;
    private float  $opacity  = 1.0;

    private function __construct(private readonly string $text)
    {
        $this->color = Color::black();
    }

    /**
     * Create a text element with the given string content.
     *
     * @param string $text  The text to display.  Special PDF characters
     *                      `(`, `)`, and `\` are automatically escaped.
     */
    public static function write(string $text): self
    {
        return new self($text);
    }

    /**
     * Set the baseline anchor point in user-space points.
     *
     * The text baseline starts at (x, y).  The default origin is the
     * lower-left corner of the page.
     *
     * @param float $x  Horizontal position.
     * @param float $y  Vertical position (baseline).
     */
    public function at(float $x, float $y): self
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    /**
     * Set the font resource name and size.
     *
     * @param string $fontName  Resource name returned by
     *                          {@see \Papier\PdfDocument::addFont()} (e.g. `'F1'`).
     * @param float  $size      Font size in points.
     */
    public function font(string $fontName, float $size): self
    {
        $this->fontName = $fontName;
        $this->fontSize = $size;
        return $this;
    }

    /**
     * Set the text colour.
     *
     * @param Color $color  A {@see Color} instance; use {@see Color::rgb()},
     *                      {@see Color::hex()}, {@see Color::gray()}, etc.
     */
    public function color(Color $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Shorthand for setting an RGB colour without constructing a Color object.
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

    /**
     * Set the fill opacity of the text.
     *
     * Implemented via an ExtGState (`/ca` parameter) registered in the page
     * resources.  Values outside [0, 1] are clamped.
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
        if ($this->opacity < 1.0) {
            self::registerOpacity($this->opacity, $cs, $resources);
        }
        $cs->beginText();
        $this->color->applyFill($cs);
        $cs->setFont($this->fontName, $this->fontSize)
           ->setTextPosition($this->x, $this->y)
           ->showText($this->text)
           ->endText();
    }

    /**
     * Register an opacity ExtGState and apply it via the `gs` operator.
     *
     * The ExtGState resource name follows the convention `GS_op_NN` where NN
     * is the opacity percentage (0–100).  Once registered it is reused across
     * all elements on the same page.
     *
     * @internal  Shared by all elements that support opacity.
     */
    static function registerOpacity(float $opacity, ContentStream $cs, PdfResources $resources): void
    {
        $name = 'GS_op_' . (int) round($opacity * 100);
        if (!$resources->getExtGStates()->has($name)) {
            $gs = new PdfDictionary();
            $gs->set('Type', new PdfName('ExtGState'));
            $gs->set('ca',   new PdfReal($opacity)); // fill opacity
            $gs->set('CA',   new PdfReal($opacity)); // stroke opacity
            $resources->addExtGState($name, $gs);
        }
        $cs->setExtGState($name);
    }
}
