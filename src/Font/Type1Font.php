<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Font\Encoding\Encoding;
use Papier\Font\Encoding\WinAnsiEncoding;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject};

/**
 * Type 1 font dictionary (ISO 32000-1 §9.6.2).
 *
 * Covers both the 14 built-in standard fonts (which do not require embedding)
 * and custom Type 1 PostScript fonts (which should include a `/FontDescriptor`
 * with an embedded font program).
 *
 * Standard font names (the 14 built-ins):
 *   `Helvetica`, `Helvetica-Bold`, `Helvetica-Oblique`, `Helvetica-BoldOblique`,
 *   `Times-Roman`, `Times-Bold`, `Times-Italic`, `Times-BoldItalic`,
 *   `Courier`, `Courier-Bold`, `Courier-Oblique`, `Courier-BoldOblique`,
 *   `Symbol`, `ZapfDingbats`.
 *
 * For standard fonts use the convenience method
 * {@see \Papier\PdfDocument::addFont()} instead of constructing directly.
 *
 * Example — custom non-standard Type 1 font:
 *
 *   $desc = new FontDescriptor('MyFont');
 *   $desc->setFlags(FontDescriptor::FLAG_NONSYMBOLIC)
 *        ->setAscent(800)->setDescent(-200)->setStemV(80)
 *        ->setFontBBox(0, -200, 1000, 800);
 *
 *   $font = new Type1Font('MyFont');
 *   $font->setFontDescriptor($desc)
 *        ->setWidths(32, 255, $widthArray);
 */
final class Type1Font extends Font
{
    private Encoding        $encoding;
    private ?FontDescriptor $descriptor  = null;
    private ?PdfObject      $widthsArray = null;
    private int             $firstChar   = 0;
    private int             $lastChar    = 255;

    /**
     * @param string        $baseFont  PDF base-font name (e.g. `Helvetica`).
     * @param Encoding|null $encoding  Character encoding; defaults to WinAnsiEncoding.
     *                                 Encoding is omitted for the 14 standard fonts.
     */
    public function __construct(
        private readonly string $baseFont,
        ?Encoding $encoding = null,
    ) {
        parent::__construct();
        $this->encoding = $encoding ?? new WinAnsiEncoding();
        $this->dictionary->set('Subtype', new PdfName('Type1'));
        $this->dictionary->set('BaseFont', new PdfName($baseFont));
    }

    public function getSubtype(): string { return 'Type1'; }

    /** Return the base-font name (value of `/BaseFont`). */
    public function getBaseFont(): string { return $this->baseFont; }

    /**
     * Override the character encoding (`/Encoding`).
     *
     * Not needed for the 14 standard fonts; required when using non-default
     * encodings with custom Type 1 fonts.
     *
     * @param Encoding $encoding  Encoding object (e.g. `WinAnsiEncoding`).
     */
    public function setEncoding(Encoding $encoding): static
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Attach a font descriptor (`/FontDescriptor`).
     *
     * Required for non-standard fonts.  The descriptor carries metrics and
     * (optionally) an embedded font program (`/FontFile`).
     *
     * @param FontDescriptor $descriptor  Pre-built font descriptor object.
     */
    public function setFontDescriptor(FontDescriptor $descriptor): static
    {
        $this->descriptor = $descriptor;
        return $this;
    }

    /**
     * Set glyph-width data for a non-standard font (`/Widths`, §9.6.2 Table 111).
     *
     * The widths array covers character codes $firstChar through $lastChar;
     * each entry is an advance width in 1/1000-unit (text-space units).
     *
     * @param int   $firstChar  First character code in the range.
     * @param int   $lastChar   Last character code in the range.
     * @param int[] $widths     Advance widths, one per character code in [firstChar…lastChar].
     */
    public function setWidths(int $firstChar, int $lastChar, array $widths): static
    {
        $this->firstChar = $firstChar;
        $this->lastChar  = $lastChar;
        $arr = new PdfArray();
        foreach ($widths as $w) {
            $arr->add(new PdfInteger($w));
        }
        $this->widthsArray = $arr;
        return $this;
    }

    public function stringWidth(string $text, float $size): float
    {
        return StandardFonts::stringWidth($text, $this->baseFont, $size);
    }

    public function getDictionary(): PdfDictionary
    {
        // Encoding is omitted for standard fonts (viewer uses built-in encoding)
        if (!StandardFonts::isStandard($this->baseFont)) {
            $this->dictionary->set('Encoding', $this->encoding->toPdfObject());
        }
        if ($this->widthsArray !== null) {
            $this->dictionary->set('FirstChar', new PdfInteger($this->firstChar));
            $this->dictionary->set('LastChar', new PdfInteger($this->lastChar));
            $this->dictionary->set('Widths', $this->widthsArray);
        }
        return $this->dictionary;
    }
}
