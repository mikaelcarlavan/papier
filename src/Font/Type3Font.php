<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Content\ContentStream;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfReal, PdfStream};

/**
 * Type 3 font (ISO 32000-1 §9.6.5).
 *
 * Each glyph is defined by a content stream.  Type 3 fonts are not
 * scalable vector outlines; they can contain any PDF graphic operators.
 */
final class Type3Font extends Font
{
    /** @var array<int, array{stream: ContentStream, width: float}> charCode → glyph */
    private array $glyphs        = [];
    private array $fontMatrix    = [0.001, 0, 0, 0.001, 0, 0];
    private array $fontBBox      = [0, 0, 0, 0];
    private ?array $encoding     = null; // charCode → name

    public function __construct()
    {
        parent::__construct();
        $this->dictionary->set('Subtype', new PdfName('Type3'));
    }

    public function getSubtype(): string { return 'Type3'; }

    public function setFontMatrix(float $a, float $b, float $c, float $d, float $e, float $f): static
    {
        $this->fontMatrix = [$a, $b, $c, $d, $e, $f];
        return $this;
    }

    public function setFontBBox(float $llx, float $lly, float $urx, float $ury): static
    {
        $this->fontBBox = [$llx, $lly, $urx, $ury];
        return $this;
    }

    /**
     * Define a glyph for a character code.
     *
     * @param int           $charCode    Character code (0–255).
     * @param ContentStream $stream      Glyph content stream.
     * @param float         $width       Glyph advance width (in glyph space).
     * @param string        $name        Glyph name (e.g., 'A', 'space').
     */
    public function addGlyph(int $charCode, ContentStream $stream, float $width, string $name = ''): static
    {
        $this->glyphs[$charCode] = ['stream' => $stream, 'width' => $width, 'name' => $name];
        return $this;
    }

    public function stringWidth(string $text, float $size): float
    {
        $width = 0;
        $len   = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $code  = ord($text[$i]);
            $width += $this->glyphs[$code]['width'] ?? 500;
        }
        return $width * $size * $this->fontMatrix[0];
    }

    public function getDictionary(): PdfDictionary
    {
        // FontMatrix
        $fm = new PdfArray();
        foreach ($this->fontMatrix as $v) {
            $fm->add(new PdfReal($v));
        }
        $this->dictionary->set('FontMatrix', $fm);

        // FontBBox
        $bb = new PdfArray();
        foreach ($this->fontBBox as $v) {
            $bb->add(new PdfReal($v));
        }
        $this->dictionary->set('FontBBox', $bb);

        return $this->dictionary;
    }

    /** @return array<int, array{stream: ContentStream, width: float, name: string}> */
    public function getGlyphs(): array { return $this->glyphs; }
}
