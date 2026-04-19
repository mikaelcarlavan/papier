<?php

declare(strict_types=1);

namespace Papier\Content;

/**
 * PDF content stream builder (ISO 32000-1 §7.8.2, §8, §9).
 *
 * Provides a fluent API for all PDF graphics and text operators.
 * All coordinates are in user space (default: points, origin at lower-left).
 */
final class ContentStream
{
    private string $buffer = '';
    private bool   $compress;

    public function __construct(bool $compress = true)
    {
        $this->compress = $compress;
    }

    public function getBuffer(): string { return $this->buffer; }

    public function isCompressed(): bool { return $this->compress; }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.4  Graphics State Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Save the graphics state (q). */
    public function save(): static
    {
        $this->buffer .= "q\n";
        return $this;
    }

    /** Restore the graphics state (Q). */
    public function restore(): static
    {
        $this->buffer .= "Q\n";
        return $this;
    }

    /** Modify the current transformation matrix (cm). */
    public function transform(float $a, float $b, float $c, float $d, float $e, float $f): static
    {
        $this->buffer .= "{$this->f($a)} {$this->f($b)} {$this->f($c)} {$this->f($d)} {$this->f($e)} {$this->f($f)} cm\n";
        return $this;
    }

    /** Translate the coordinate system. */
    public function translate(float $tx, float $ty): static
    {
        return $this->transform(1, 0, 0, 1, $tx, $ty);
    }

    /** Scale the coordinate system. */
    public function scale(float $sx, float $sy): static
    {
        return $this->transform($sx, 0, 0, $sy, 0, 0);
    }

    /** Rotate the coordinate system by $angle degrees counter-clockwise. */
    public function rotate(float $angle): static
    {
        $rad = deg2rad($angle);
        $cos = cos($rad);
        $sin = sin($rad);
        return $this->transform($cos, $sin, -$sin, $cos, 0, 0);
    }

    /** Set line width (w). */
    public function setLineWidth(float $width): static
    {
        $this->buffer .= "{$this->f($width)} w\n";
        return $this;
    }

    /** Set line cap style (J): 0=butt, 1=round, 2=projecting square. */
    public function setLineCap(int $cap): static
    {
        $this->buffer .= "$cap J\n";
        return $this;
    }

    /** Set line join style (j): 0=miter, 1=round, 2=bevel. */
    public function setLineJoin(int $join): static
    {
        $this->buffer .= "$join j\n";
        return $this;
    }

    /** Set miter limit (M). */
    public function setMiterLimit(float $limit): static
    {
        $this->buffer .= "{$this->f($limit)} M\n";
        return $this;
    }

    /** Set the dash array and phase (d). */
    public function setDash(array $dashArray, float $phase = 0): static
    {
        $arr = '[' . implode(' ', array_map([$this, 'f'], $dashArray)) . ']';
        $this->buffer .= "{$arr} {$this->f($phase)} d\n";
        return $this;
    }

    /** Set the colour rendering intent (ri). */
    public function setRenderingIntent(string $intent): static
    {
        $this->buffer .= "/$intent ri\n";
        return $this;
    }

    /** Apply a named extended graphics state (gs). */
    public function setExtGState(string $name): static
    {
        $this->buffer .= "/$name gs\n";
        return $this;
    }

    /** Set flatness tolerance (i). */
    public function setFlatness(float $flatness): static
    {
        $this->buffer .= "{$this->f($flatness)} i\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.5  Path Construction Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Begin a new subpath (m). */
    public function moveTo(float $x, float $y): static
    {
        $this->buffer .= "{$this->f($x)} {$this->f($y)} m\n";
        return $this;
    }

    /** Append a straight line (l). */
    public function lineTo(float $x, float $y): static
    {
        $this->buffer .= "{$this->f($x)} {$this->f($y)} l\n";
        return $this;
    }

    /** Append a cubic Bézier curve — both control points explicit (c). */
    public function curveTo(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): static
    {
        $this->buffer .= "{$this->f($x1)} {$this->f($y1)} {$this->f($x2)} {$this->f($y2)} {$this->f($x3)} {$this->f($y3)} c\n";
        return $this;
    }

    /** Append a cubic Bézier — first control point = current point (v). */
    public function curveToV(float $x2, float $y2, float $x3, float $y3): static
    {
        $this->buffer .= "{$this->f($x2)} {$this->f($y2)} {$this->f($x3)} {$this->f($y3)} v\n";
        return $this;
    }

    /** Append a cubic Bézier — second control point = final point (y). */
    public function curveToY(float $x1, float $y1, float $x3, float $y3): static
    {
        $this->buffer .= "{$this->f($x1)} {$this->f($y1)} {$this->f($x3)} {$this->f($y3)} y\n";
        return $this;
    }

    /** Close the current subpath (h). */
    public function closePath(): static
    {
        $this->buffer .= "h\n";
        return $this;
    }

    /** Append a rectangle (re). */
    public function rectangle(float $x, float $y, float $width, float $height): static
    {
        $this->buffer .= "{$this->f($x)} {$this->f($y)} {$this->f($width)} {$this->f($height)} re\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.5.3  Path Painting Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Stroke the path (S). */
    public function stroke(): static
    {
        $this->buffer .= "S\n";
        return $this;
    }

    /** Close and stroke (s). */
    public function closeStroke(): static
    {
        $this->buffer .= "s\n";
        return $this;
    }

    /** Fill using non-zero winding number rule (f / F). */
    public function fill(): static
    {
        $this->buffer .= "f\n";
        return $this;
    }

    /** Fill using even-odd rule (f*). */
    public function fillEvenOdd(): static
    {
        $this->buffer .= "f*\n";
        return $this;
    }

    /** Fill and stroke (B). */
    public function fillStroke(): static
    {
        $this->buffer .= "B\n";
        return $this;
    }

    /** Fill (even-odd) and stroke (B*). */
    public function fillStrokeEvenOdd(): static
    {
        $this->buffer .= "B*\n";
        return $this;
    }

    /** Close, fill, and stroke (b). */
    public function closeFillStroke(): static
    {
        $this->buffer .= "b\n";
        return $this;
    }

    /** Close, fill (even-odd), and stroke (b*). */
    public function closeFillStrokeEvenOdd(): static
    {
        $this->buffer .= "b*\n";
        return $this;
    }

    /** End the path without painting (n). */
    public function endPath(): static
    {
        $this->buffer .= "n\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.5.4  Clipping Path Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Modify the clipping path using non-zero winding rule (W). */
    public function clip(): static
    {
        $this->buffer .= "W\n";
        return $this;
    }

    /** Modify the clipping path using even-odd rule (W*). */
    public function clipEvenOdd(): static
    {
        $this->buffer .= "W*\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.6  Colour Space Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Set stroking colour space (CS). */
    public function setStrokeColorSpace(string $name): static
    {
        $this->buffer .= "/$name CS\n";
        return $this;
    }

    /** Set non-stroking colour space (cs). */
    public function setFillColorSpace(string $name): static
    {
        $this->buffer .= "/$name cs\n";
        return $this;
    }

    /** Set stroking colour — generic (SC). */
    public function setStrokeColor(float ...$components): static
    {
        $this->buffer .= implode(' ', array_map([$this, 'f'], $components)) . " SC\n";
        return $this;
    }

    /** Set stroking colour with optional name (SCN). */
    public function setStrokeColorN(float|string ...$components): static
    {
        $parts = array_map(fn($c) => is_string($c) ? "/$c" : $this->f($c), $components);
        $this->buffer .= implode(' ', $parts) . " SCN\n";
        return $this;
    }

    /** Set non-stroking colour — generic (sc). */
    public function setFillColor(float ...$components): static
    {
        $this->buffer .= implode(' ', array_map([$this, 'f'], $components)) . " sc\n";
        return $this;
    }

    /** Set non-stroking colour with optional name (scn). */
    public function setFillColorN(float|string ...$components): static
    {
        $parts = array_map(fn($c) => is_string($c) ? "/$c" : $this->f($c), $components);
        $this->buffer .= implode(' ', $parts) . " scn\n";
        return $this;
    }

    /** Set stroking grey level (G). */
    public function setStrokeGray(float $gray): static
    {
        $this->buffer .= "{$this->f($gray)} G\n";
        return $this;
    }

    /** Set non-stroking grey level (g). */
    public function setFillGray(float $gray): static
    {
        $this->buffer .= "{$this->f($gray)} g\n";
        return $this;
    }

    /** Set stroking RGB colour (RG). */
    public function setStrokeRGB(float $r, float $g, float $b): static
    {
        $this->buffer .= "{$this->f($r)} {$this->f($g)} {$this->f($b)} RG\n";
        return $this;
    }

    /** Set non-stroking RGB colour (rg). */
    public function setFillRGB(float $r, float $g, float $b): static
    {
        $this->buffer .= "{$this->f($r)} {$this->f($g)} {$this->f($b)} rg\n";
        return $this;
    }

    /** Set stroking CMYK colour (K). */
    public function setStrokeCMYK(float $c, float $m, float $y, float $k): static
    {
        $this->buffer .= "{$this->f($c)} {$this->f($m)} {$this->f($y)} {$this->f($k)} K\n";
        return $this;
    }

    /** Set non-stroking CMYK colour (k). */
    public function setFillCMYK(float $c, float $m, float $y, float $k): static
    {
        $this->buffer .= "{$this->f($c)} {$this->f($m)} {$this->f($y)} {$this->f($k)} k\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.7  Shading Operator
    // ═══════════════════════════════════════════════════════════════════════════

    /** Paint an area with a shading pattern (sh). */
    public function shading(string $name): static
    {
        $this->buffer .= "/$name sh\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §8.8 / §8.10  XObject / Form
    // ═══════════════════════════════════════════════════════════════════════════

    /** Invoke a named XObject (Do). */
    public function drawXObject(string $name): static
    {
        $this->buffer .= "/$name Do\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §9.3  Text State Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Set character spacing (Tc). */
    public function setCharSpacing(float $spacing): static
    {
        $this->buffer .= "{$this->f($spacing)} Tc\n";
        return $this;
    }

    /** Set word spacing (Tw). */
    public function setWordSpacing(float $spacing): static
    {
        $this->buffer .= "{$this->f($spacing)} Tw\n";
        return $this;
    }

    /** Set horizontal text scaling (Tz).  100 = normal. */
    public function setHorizontalScaling(float $scale): static
    {
        $this->buffer .= "{$this->f($scale)} Tz\n";
        return $this;
    }

    /** Set text leading (TL). */
    public function setTextLeading(float $leading): static
    {
        $this->buffer .= "{$this->f($leading)} TL\n";
        return $this;
    }

    /** Set text font and size (Tf). */
    public function setFont(string $fontName, float $size): static
    {
        $this->buffer .= "/$fontName {$this->f($size)} Tf\n";
        return $this;
    }

    /** Set text rendering mode (Tr): 0=fill, 1=stroke, 2=fill+stroke, 3=invisible, 4-7=clipping variants. */
    public function setTextRenderMode(int $mode): static
    {
        $this->buffer .= "$mode Tr\n";
        return $this;
    }

    /** Set text rise (Ts). */
    public function setTextRise(float $rise): static
    {
        $this->buffer .= "{$this->f($rise)} Ts\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §9.4  Text Object Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Begin a text object (BT). */
    public function beginText(): static
    {
        $this->buffer .= "BT\n";
        return $this;
    }

    /** End a text object (ET). */
    public function endText(): static
    {
        $this->buffer .= "ET\n";
        return $this;
    }

    /** Move to the start of the next line (Td). */
    public function moveText(float $tx, float $ty): static
    {
        $this->buffer .= "{$this->f($tx)} {$this->f($ty)} Td\n";
        return $this;
    }

    /** Move to the next line and set leading (TD). */
    public function moveTextSetLeading(float $tx, float $ty): static
    {
        $this->buffer .= "{$this->f($tx)} {$this->f($ty)} TD\n";
        return $this;
    }

    /** Set the text matrix and line matrix (Tm). */
    public function setTextMatrix(float $a, float $b, float $c, float $d, float $e, float $f): static
    {
        $this->buffer .= "{$this->f($a)} {$this->f($b)} {$this->f($c)} {$this->f($d)} {$this->f($e)} {$this->f($f)} Tm\n";
        return $this;
    }

    /** Move to the start of the next line (T*). */
    public function nextLine(): static
    {
        $this->buffer .= "T*\n";
        return $this;
    }

    /** Set text position using absolute coordinates (convenience). */
    public function setTextPosition(float $x, float $y): static
    {
        return $this->setTextMatrix(1, 0, 0, 1, $x, $y);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §9.4.3 / §9.4.4  Text Showing Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Show a string (Tj). */
    public function showText(string $text): static
    {
        $this->buffer .= $this->pdfString($text) . " Tj\n";
        return $this;
    }

    /** Move to next line and show a string ('). */
    public function moveShowText(string $text): static
    {
        $this->buffer .= $this->pdfString($text) . " '\n";
        return $this;
    }

    /**
     * Move to next line, set word/char spacing, and show a string (").
     */
    public function moveShowTextSpaced(float $aw, float $ac, string $text): static
    {
        $this->buffer .= "{$this->f($aw)} {$this->f($ac)} {$this->pdfString($text)} \"\n";
        return $this;
    }

    /**
     * Show text with individual glyph positioning (TJ).
     * $array: alternating strings and numeric positioning adjustments (in thousandths of text-space units).
     */
    public function showTextArray(array $array): static
    {
        $parts = [];
        foreach ($array as $item) {
            if (is_string($item)) {
                $parts[] = $this->pdfString($item);
            } else {
                $parts[] = $this->f((float) $item);
            }
        }
        $this->buffer .= '[' . implode(' ', $parts) . "] TJ\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §14.6  Marked Content Operators
    // ═══════════════════════════════════════════════════════════════════════════

    /** Designate a marked-content point (MP). */
    public function markedContentPoint(string $tag): static
    {
        $this->buffer .= "/$tag MP\n";
        return $this;
    }

    /** Designate a marked-content point with property dict (DP). */
    public function markedContentPointProps(string $tag, string $propsName): static
    {
        $this->buffer .= "/$tag /$propsName DP\n";
        return $this;
    }

    /** Begin a marked-content sequence (BMC). */
    public function beginMarkedContent(string $tag): static
    {
        $this->buffer .= "/$tag BMC\n";
        return $this;
    }

    /** Begin a marked-content sequence with property dict (BDC). */
    public function beginMarkedContentProps(string $tag, string $propsName): static
    {
        $this->buffer .= "/$tag /$propsName BDC\n";
        return $this;
    }

    /** End a marked-content sequence (EMC). */
    public function endMarkedContent(): static
    {
        $this->buffer .= "EMC\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §11.6  Transparency Operators (PDF 1.4+)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Emit a `gs` operator to apply a named opacity graphics state (§11.6.4.4).
     *
     * This method follows the naming convention used by the high-level elements:
     * the ExtGState resource name is `GS_op_NN` where NN is the opacity
     * percentage (0–100, rounded to the nearest integer).
     *
     * **The caller is responsible for registering the corresponding ExtGState
     * in the page resource dictionary before this operator is executed.**
     * High-level elements (Text, Rectangle, Image, Circle, Line) do this
     * automatically.  When using ContentStream directly, register the state:
     *
     *   $gs = new PdfDictionary();
     *   $gs->set('Type', new PdfName('ExtGState'));
     *   $gs->set('ca', new PdfReal(0.5));   // fill opacity
     *   $gs->set('CA', new PdfReal(0.5));   // stroke opacity
     *   $page->getResources()->addExtGState('GS_op_50', $gs);
     *
     *   $cs->setOpacity(0.5); // emits /GS_op_50 gs
     *
     * @param float $opacity  Target opacity in [0, 1].
     */
    public function setOpacity(float $opacity): static
    {
        $name = 'GS_op_' . (int) round($opacity * 100);
        $this->buffer .= "/{$name} gs\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // §13.3  Inline Images
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Embed an inline image (BI … ID … EI).
     *
     * @param array  $imageParams Key-value pairs for the image dictionary.
     * @param string $imageData   Raw or encoded image bytes.
     */
    public function inlineImage(array $imageParams, string $imageData): static
    {
        $this->buffer .= "BI\n";
        foreach ($imageParams as $key => $value) {
            if (is_string($value) && str_starts_with($value, '/')) {
                $this->buffer .= "/$key $value\n";
            } elseif (is_int($value) || is_float($value)) {
                $this->buffer .= "/$key {$this->f((float)$value)}\n";
            } else {
                $this->buffer .= "/$key ($value)\n";
            }
        }
        $this->buffer .= "ID\n$imageData\nEI\n";
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // Convenience drawing methods
    // ═══════════════════════════════════════════════════════════════════════════

    /** Draw a straight line from (x1,y1) to (x2,y2). */
    public function drawLine(float $x1, float $y1, float $x2, float $y2): static
    {
        return $this->moveTo($x1, $y1)->lineTo($x2, $y2)->stroke();
    }

    /** Draw a filled and/or stroked rectangle. */
    public function drawRect(float $x, float $y, float $w, float $h, bool $fill = false, bool $strokeRect = true): static
    {
        $this->rectangle($x, $y, $w, $h);
        if ($fill && $strokeRect) {
            return $this->fillStroke();
        } elseif ($fill) {
            return $this->fill();
        } elseif ($strokeRect) {
            return $this->stroke();
        }
        return $this->endPath();
    }

    /**
     * Draw an ellipse approximated by four Bézier curves.
     *
     * @param float $cx  Center X
     * @param float $cy  Center Y
     * @param float $rx  X radius
     * @param float $ry  Y radius
     */
    public function drawEllipse(float $cx, float $cy, float $rx, float $ry): static
    {
        // κ ≈ 0.5523 for a quarter-circle
        $k = 0.5523;
        $this->moveTo($cx + $rx, $cy);
        $this->curveTo($cx + $rx, $cy + $k * $ry, $cx + $k * $rx, $cy + $ry, $cx, $cy + $ry);
        $this->curveTo($cx - $k * $rx, $cy + $ry, $cx - $rx, $cy + $k * $ry, $cx - $rx, $cy);
        $this->curveTo($cx - $rx, $cy - $k * $ry, $cx - $k * $rx, $cy - $ry, $cx, $cy - $ry);
        $this->curveTo($cx + $k * $rx, $cy - $ry, $cx + $rx, $cy - $k * $ry, $cx + $rx, $cy);
        return $this;
    }

    /** Draw a circle. */
    public function drawCircle(float $cx, float $cy, float $r): static
    {
        return $this->drawEllipse($cx, $cy, $r, $r);
    }

    /**
     * Write a line of text at an absolute position.
     *
     * @param string $text     UTF-8 text (or PDFDocEncoding for simple fonts).
     * @param string $fontName Resource name (e.g., 'F1').
     * @param float  $size     Font size in points.
     * @param float  $x        X position.
     * @param float  $y        Y position.
     */
    public function writeText(string $text, string $fontName, float $size, float $x, float $y): static
    {
        return $this
            ->beginText()
            ->setFont($fontName, $size)
            ->setTextPosition($x, $y)
            ->showText($text)
            ->endText();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // Raw content injection
    // ═══════════════════════════════════════════════════════════════════════════

    /** Append raw PDF content bytes. Use sparingly. */
    public function raw(string $content): static
    {
        $this->buffer .= $content;
        return $this;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════════════════

    /** Format a float for PDF output (up to 6 decimal places). */
    private function f(float $v): string
    {
        $s = rtrim(number_format($v, 6, '.', ''), '0');
        return str_ends_with($s, '.') ? $s . '0' : $s;
    }

    /**
     * Encode a PHP (UTF-8) string as a PDF literal string using WinAnsiEncoding.
     *
     * Standard Type 1 and TrueType fonts in this library use WinAnsiEncoding,
     * which maps to Windows-1252.  Latin characters such as é, à, ü, ñ are
     * single bytes in that encoding.  Characters outside Windows-1252 are
     * replaced with '?'.
     */
    private function pdfString(string $s): string
    {
        // Convert UTF-8 → Windows-1252 so Latin accented characters render correctly.
        $s = mb_convert_encoding($s, 'Windows-1252', 'UTF-8');

        $escaped = '';
        $len     = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $c = $s[$i];
            $escaped .= match ($c) {
                '('  => '\\(',
                ')'  => '\\)',
                '\\' => '\\\\',
                "\r" => '\\r',
                "\n" => '\\n',
                default => $c,
            };
        }
        return "($escaped)";
    }
}
