<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfString};

/**
 * Type 0 (composite) font (ISO 32000-1 §9.7).
 *
 * Type 0 fonts use a CMap to map character codes to glyph IDs from a
 * descendant CIDFont (CIDFontType0 or CIDFontType2).  Used for CJK text
 * and full Unicode support.
 */
final class Type0Font extends Font
{
    private string    $encoding      = 'Identity-H';
    private ?PdfObject $descendantRef = null;
    private ?PdfObject $toUnicodeRef  = null;

    public function __construct(private readonly string $baseFont)
    {
        parent::__construct();
        $this->dictionary->set('Subtype', new PdfName('Type0'));
        $this->dictionary->set('BaseFont', new PdfName($baseFont));
    }

    public function getSubtype(): string { return 'Type0'; }

    /** CMap name or CMap stream reference (e.g., 'Identity-H', 'Identity-V'). */
    public function setEncoding(string|PdfObject $encoding): static
    {
        $this->encoding     = is_string($encoding) ? $encoding : '';
        $this->dictionary->set('Encoding', is_string($encoding) ? new PdfName($encoding) : $encoding);
        return $this;
    }

    public function setDescendantFont(PdfObject $descendantRef): static
    {
        $this->descendantRef = $descendantRef;
        return $this;
    }

    public function setToUnicode(PdfObject $toUnicodeRef): static
    {
        $this->toUnicodeRef = $toUnicodeRef;
        return $this;
    }

    public function stringWidth(string $text, float $size): float
    {
        // For Identity-H/V encoded text, each character is 2 bytes (Unicode code unit)
        return (mb_strlen($text, 'UTF-8') * 500 * $size) / 1000;
    }

    public function getDictionary(): PdfDictionary
    {
        $this->dictionary->set('Encoding', new PdfName($this->encoding));
        if ($this->descendantRef !== null) {
            $arr = new PdfArray();
            $arr->add($this->descendantRef);
            $this->dictionary->set('DescendantFonts', $arr);
        }
        if ($this->toUnicodeRef !== null) {
            $this->dictionary->set('ToUnicode', $this->toUnicodeRef);
        }
        return $this->dictionary;
    }
}
