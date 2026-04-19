<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Objects\{PdfDictionary, PdfInteger, PdfName, PdfObject, PdfReal, PdfString};

/**
 * Font descriptor (ISO 32000-1 §9.8).
 *
 * Describes the metrics and style of a font program. Required for all
 * non-standard fonts.
 */
final class FontDescriptor
{
    // Flags bit positions (§9.8.2 Table 123)
    public const FLAG_FIXED_PITCH  = 1;
    public const FLAG_SERIF        = 2;
    public const FLAG_SYMBOLIC     = 4;
    public const FLAG_SCRIPT       = 8;
    public const FLAG_NONSYMBOLIC  = 32;
    public const FLAG_ITALIC       = 64;
    public const FLAG_ALL_CAP      = 65536;
    public const FLAG_SMALL_CAP    = 131072;
    public const FLAG_FORCE_BOLD   = 262144;

    private string $fontName;
    private int    $flags         = 32;   // NonSymbolic default
    private array  $fontBBox      = [0, 0, 0, 0];
    private int    $italicAngle   = 0;
    private int    $ascent        = 0;
    private int    $descent       = 0;
    private int    $capHeight     = 0;
    private int    $stemV         = 0;
    private int    $stemH         = 0;
    private int    $avgWidth      = 0;
    private int    $maxWidth      = 0;
    private int    $missingWidth  = 0;
    private int    $xHeight       = 0;
    private ?PdfObject $fontFile  = null;  // For Type1/Type3
    private ?PdfObject $fontFile2 = null;  // For TrueType
    private ?PdfObject $fontFile3 = null;  // For CFF/OpenType
    private ?string    $charSet   = null;

    public function __construct(string $fontName)
    {
        $this->fontName = $fontName;
    }

    public function setFlags(int $flags): static { $this->flags = $flags; return $this; }
    public function setFontBBox(int $llx, int $lly, int $urx, int $ury): static { $this->fontBBox = [$llx, $lly, $urx, $ury]; return $this; }
    public function setItalicAngle(int $angle): static { $this->italicAngle = $angle; return $this; }
    public function setAscent(int $ascent): static { $this->ascent = $ascent; return $this; }
    public function setDescent(int $descent): static { $this->descent = $descent; return $this; }
    public function setCapHeight(int $capHeight): static { $this->capHeight = $capHeight; return $this; }
    public function setStemV(int $stemV): static { $this->stemV = $stemV; return $this; }
    public function setStemH(int $stemH): static { $this->stemH = $stemH; return $this; }
    public function setAvgWidth(int $w): static { $this->avgWidth = $w; return $this; }
    public function setMaxWidth(int $w): static { $this->maxWidth = $w; return $this; }
    public function setMissingWidth(int $w): static { $this->missingWidth = $w; return $this; }
    public function setXHeight(int $h): static { $this->xHeight = $h; return $this; }
    public function setFontFile(PdfObject $stream): static { $this->fontFile = $stream; return $this; }
    public function setFontFile2(PdfObject $stream): static { $this->fontFile2 = $stream; return $this; }
    public function setFontFile3(PdfObject $stream): static { $this->fontFile3 = $stream; return $this; }
    public function setCharSet(string $charSet): static { $this->charSet = $charSet; return $this; }

    public function toDictionary(): PdfDictionary
    {
        $dict = new PdfDictionary();
        $dict->set('Type', new PdfName('FontDescriptor'));
        $dict->set('FontName', new PdfName($this->fontName));
        $dict->set('Flags', new PdfInteger($this->flags));
        $bbox = $this->fontBBox;
        $bboxArr = new \Papier\Objects\PdfArray();
        foreach ($bbox as $v) {
            $bboxArr->add(new PdfInteger($v));
        }
        $dict->set('FontBBox', $bboxArr);
        $dict->set('ItalicAngle', new PdfInteger($this->italicAngle));
        $dict->set('Ascent', new PdfInteger($this->ascent));
        $dict->set('Descent', new PdfInteger($this->descent));
        if ($this->capHeight !== 0) {
            $dict->set('CapHeight', new PdfInteger($this->capHeight));
        }
        $dict->set('StemV', new PdfInteger($this->stemV));
        if ($this->stemH !== 0) {
            $dict->set('StemH', new PdfInteger($this->stemH));
        }
        if ($this->avgWidth !== 0) {
            $dict->set('AvgWidth', new PdfInteger($this->avgWidth));
        }
        if ($this->maxWidth !== 0) {
            $dict->set('MaxWidth', new PdfInteger($this->maxWidth));
        }
        if ($this->missingWidth !== 0) {
            $dict->set('MissingWidth', new PdfInteger($this->missingWidth));
        }
        if ($this->xHeight !== 0) {
            $dict->set('XHeight', new PdfInteger($this->xHeight));
        }
        if ($this->fontFile !== null) {
            $dict->set('FontFile', $this->fontFile);
        }
        if ($this->fontFile2 !== null) {
            $dict->set('FontFile2', $this->fontFile2);
        }
        if ($this->fontFile3 !== null) {
            $dict->set('FontFile3', $this->fontFile3);
        }
        if ($this->charSet !== null) {
            $dict->set('CharSet', new PdfString($this->charSet));
        }
        return $dict;
    }
}
