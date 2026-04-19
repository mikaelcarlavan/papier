<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Elements\Color;
use Papier\Objects\{PdfArray, PdfInteger, PdfName, PdfObject, PdfReal, PdfString};

/**
 * Free-text (typewriter) annotation (`/Subtype /FreeText`).
 *
 * Displays text directly on the page without a pop-up.  Requires a default
 * appearance string (`/DA`) specifying the font and colour.
 */
final class FreeTextAnnotation extends Annotation
{
    public function getSubtype(): string { return 'FreeText'; }

    /**
     * Set the default appearance for the annotation text (`/DA`).
     *
     * Pass the font resource name returned by
     * {@see \Papier\PdfDocument::addFont()}, the point size, and an optional
     * colour (defaults to black).
     *
     * Example:
     *
     *   $annot->setDefaultAppearance($fontName, 10);
     *   $annot->setDefaultAppearance($fontName, 10, Color::rgb(0.8, 0.1, 0.1));
     *
     * @param string      $fontName  Font resource name (e.g. `'F1'`).
     * @param float       $fontSize  Font size in points.
     * @param Color|null  $color     Text colour; defaults to black.
     */
    public function setDefaultAppearance(string $fontName, float $fontSize, ?Color $color = null): static
    {
        $fmt = static fn(float $v): string => rtrim(rtrim(number_format($v, 4, '.', ''), '0'), '.');
        if ($color !== null) {
            [$r, $g, $b] = $color->toRgb();
            $colorOp = "{$fmt($r)} {$fmt($g)} {$fmt($b)} rg";
        } else {
            $colorOp = '0 g';
        }
        $this->dict->set('DA', new PdfString("/{$fontName} {$fmt($fontSize)} Tf {$colorOp}"));
        return $this;
    }

    /**
     * Set text justification (`/Q`).
     *
     * @param int $q  0 left, 1 centred, 2 right.
     */
    public function setJustification(int $q): static
    {
        $this->dict->set('Q', new PdfInteger($q));
        return $this;
    }

    /**
     * Set the callout line points (`/CL`).
     *
     * A callout line connects the free-text annotation box to a specific point
     * on the page.  Pass either 4 coordinates (knee + tip) or 6 (start + knee + tip).
     *
     * Example (4-point: x1,y1 → x2,y2):
     *
     *   $annot->setCallout(100, 500, 200, 600);
     *
     * Example (6-point: x1,y1 → x2,y2 → x3,y3):
     *
     *   $annot->setCallout(100, 500, 150, 550, 200, 600);
     *
     * @param float $x1   First X coordinate.
     * @param float $y1   First Y coordinate.
     * @param float $x2   Second X coordinate.
     * @param float $y2   Second Y coordinate.
     * @param float $x3   Third X coordinate (6-point form only).
     * @param float $y3   Third Y coordinate (6-point form only).
     */
    public function setCallout(float $x1, float $y1, float $x2, float $y2, ?float $x3 = null, ?float $y3 = null): static
    {
        $arr = new PdfArray();
        foreach ([$x1, $y1, $x2, $y2] as $v) { $arr->add(new PdfReal($v)); }
        if ($x3 !== null && $y3 !== null) {
            $arr->add(new PdfReal($x3)); $arr->add(new PdfReal($y3));
        }
        $this->dict->set('CL', $arr);
        return $this;
    }

    /**
     * Set the annotation intent (`/IT`).
     *
     * @param string $intent  `FreeText`, `FreeTextCallout`, or `FreeTextTypeWriter`.
     */
    public function setIntent(string $intent): static
    {
        $this->dict->set('IT', new PdfName($intent));
        return $this;
    }

    /**
     * Set the rich-text string (`/RC`).
     *
     * @param string $rt  XHTML-formatted rich text.
     */
    public function setRichText(string $rt): static
    {
        $this->dict->set('RC', new PdfString($rt));
        return $this;
    }
}
