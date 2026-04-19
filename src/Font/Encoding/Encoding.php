<?php

declare(strict_types=1);

namespace Papier\Font\Encoding;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject};

/**
 * Abstract font encoding (ISO 32000-1 §9.6.5).
 *
 * Maps character codes to glyph names.
 */
abstract class Encoding
{
    /** Return the encoding name (e.g., 'WinAnsiEncoding'). */
    abstract public function getName(): string;

    /** Return glyph name for character code, or null if unmapped. */
    abstract public function getGlyphName(int $charCode): ?string;

    /** Return character code for glyph name, or null if not present. */
    public function getCharCode(string $glyphName): ?int
    {
        for ($i = 0; $i <= 255; $i++) {
            if ($this->getGlyphName($i) === $glyphName) {
                return $i;
            }
        }
        return null;
    }

    /**
     * Build the PDF encoding dictionary for this encoding, if it differs
     * from the base encoding and has differences.
     */
    public function toPdfObject(): PdfObject
    {
        return new PdfName($this->getName());
    }
}
