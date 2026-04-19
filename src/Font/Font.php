<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Objects\{PdfDictionary, PdfName, PdfObject};

/**
 * Abstract base class for all PDF font types (ISO 32000-1 §9).
 *
 * A PDF font dictionary describes how glyphs are selected, encoded, and
 * rendered.  The subtype (§9.6 Table 110) determines the data format:
 *
 *   - {@see Type1Font}     — `Type1` / `MMType1`  (PostScript outlines, §9.6.2)
 *   - {@see TrueTypeFont}  — `TrueType`            (TrueType/OpenType, §9.6.3)
 *   - {@see Type0Font}     — `Type0` (composite)   (CJK, §9.7)
 *   - {@see CIDFont}       — `CIDFontType0/2`      (descendant of Type0, §9.7.4)
 *   - {@see Type3Font}     — `Type3`               (user-defined glyphs, §9.6.5)
 *
 * Fonts must be registered with a {@see \Papier\Structure\PdfResources}
 * instance and referenced in content streams by their resource name.  Use
 * {@see \Papier\PdfDocument::addFont()} for the standard 14 fonts, or
 * {@see \Papier\PdfDocument::registerFont()} for custom font objects.
 *
 * The resource name returned by those methods is passed as the `$font`
 * argument to `Text::write()->font($name, $size)`.
 */
abstract class Font
{
    protected PdfDictionary $dictionary;
    protected string        $resourceName = '';

    public function __construct()
    {
        $this->dictionary = new PdfDictionary();
        $this->dictionary->set('Type', new PdfName('Font'));
    }

    /**
     * Return the PDF font subtype string (the value of `/Subtype`).
     */
    abstract public function getSubtype(): string;

    /**
     * Return the advance width of $text in points at the given font size.
     *
     * Used for text layout, line-wrapping, and centering calculations.
     *
     * @param string $text  Text string (encoding depends on the font subtype).
     * @param float  $size  Font size in points.
     *
     * @return float  String width in points.
     */
    abstract public function stringWidth(string $text, float $size): float;

    /** Return the font dictionary (populated by subclass constructors). */
    public function getDictionary(): PdfDictionary
    {
        return $this->dictionary;
    }

    /**
     * Return the resource name used in the `/Resources /Font` dictionary
     * (e.g. `F1`, `F2`).
     *
     * Set automatically by the writer when the font is registered.
     */
    public function getResourceName(): string { return $this->resourceName; }

    /**
     * Set the resource name for this font.
     *
     * @internal  Called by {@see \Papier\Writer\PdfWriter::registerFont()}.
     *
     * @param string $name  Resource name string (e.g. `F1`).
     */
    public function setResourceName(string $name): static
    {
        $this->resourceName = $name;
        return $this;
    }
}
