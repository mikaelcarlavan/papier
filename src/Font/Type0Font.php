<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Objects\{PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfName, PdfObject, PdfStream, PdfString};

/**
 * Type 0 (composite) font (ISO 32000-1 §9.7).
 *
 * A Type 0 font maps multi-byte character codes through a CMap to glyphs in a
 * descendant CIDFont.  With the Identity-H encoding and an Identity CIDToGIDMap,
 * character codes are two-byte glyph ids, giving full Unicode coverage —
 * required for CJK and other scripts outside the 256-code WinAnsi range.
 *
 * Use {@see fromTrueType()} (or {@see \Papier\PdfDocument::addUnicodeFont()})
 * to build one from a TrueType/OpenType file.
 */
final class Type0Font extends Font
{
    private string    $encoding      = 'Identity-H';
    private ?PdfObject $descendantRef = null;
    private ?PdfObject $toUnicodeRef  = null;

    // Embedding state (populated by fromTrueType()).
    private ?string $fontData   = null;
    private bool    $subset     = false;
    /** @var array<int,int> Unicode code point → glyph id */
    private array   $cmap       = [];
    /** @var array<int,int> glyph id → advance width (1000 units/em) */
    private array   $advances   = [];
    /** @var array<int,true> glyph ids actually used in the document */
    private array   $usedGlyphs = [];
    /** @var array<string,mixed> font metrics (ascent, descent, capHeight, italicAngle, bbox) */
    private array   $metrics    = [];

    public function __construct(private string $baseFont)
    {
        parent::__construct();
        $this->dictionary->set('Subtype', new PdfName('Type0'));
        $this->dictionary->set('BaseFont', new PdfName($baseFont));
    }

    /**
     * Build a composite (Type 0 / CIDFontType2) font from a TrueType/OpenType file.
     *
     * @param string $path    Path to a .ttf/.otf file.
     * @param bool   $subset  Strip unused glyph outlines from the embedded program.
     */
    public static function fromTrueType(string $path, bool $subset = true): self
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Font file not found: $path");
        }
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \RuntimeException("Cannot read font file: $path");
        }
        return self::fromData($data, $subset);
    }

    public static function fromData(string $data, bool $subset = true): self
    {
        $info    = TrueTypeParser::parse($data);
        $psName  = $info['postScriptName'] !== ''
            ? preg_replace('/[^\x21-\x7E]/', '-', $info['postScriptName'])
            : 'Embedded';

        $font = new self($psName);
        $font->fontData   = $data;
        $font->subset     = $subset && !$info['isCFF']; // glyf-based subsetter only
        $font->cmap       = $info['cmap'];
        $font->advances   = $info['advances'];
        $font->metrics    = [
            'ascent'      => $info['ascent'],
            'descent'     => $info['descent'],
            'capHeight'   => $info['capHeight'],
            'italicAngle' => $info['italicAngle'],
            'bbox'        => $info['bbox'],
        ];
        return $font;
    }

    public function getSubtype(): string { return 'Type0'; }

    public function isComposite(): bool { return true; }

    public function getBaseFont(): string { return $this->baseFont; }

    /** CMap name (e.g. 'Identity-H', 'Identity-V'). */
    public function setEncoding(string|PdfObject $encoding): static
    {
        $this->encoding = is_string($encoding) ? $encoding : 'Identity-H';
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

    /**
     * Encode a UTF-8 string as the two-byte glyph-id codes used with Identity-H,
     * recording which glyphs are referenced (for subsetting).
     */
    public function encodeText(string $text): string
    {
        $out = '';
        $len = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $cp  = mb_ord(mb_substr($text, $i, 1, 'UTF-8'), 'UTF-8');
            $gid = $this->cmap[$cp] ?? 0;
            $this->usedGlyphs[$gid] = true;
            $out .= pack('n', $gid);
        }
        return $out;
    }

    public function stringWidth(string $text, float $size): float
    {
        $width = 0;
        $len   = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $cp  = mb_ord(mb_substr($text, $i, 1, 'UTF-8'), 'UTF-8');
            $gid = $this->cmap[$cp] ?? 0;
            $width += $this->advances[$gid] ?? 1000;
        }
        return $width * $size / 1000;
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

    /**
     * Allocate every indirect object this composite font needs (CIDFont,
     * FontDescriptor, FontFile2, ToUnicode) via the supplied allocator and wire
     * up the references.  Called by the writer during serialisation.
     *
     * @param \Closure(PdfObject):int $allocate
     */
    public function registerObjects(\Closure $allocate): void
    {
        if ($this->fontData === null) {
            return; // nothing to embed
        }

        // Always include .notdef.
        $this->usedGlyphs[0] = true;

        // 1. Embedded font program (FontFile2), optionally subset.
        $program = $this->fontData;
        $tagged  = $this->baseFont;
        if ($this->subset) {
            $sub = TrueTypeSubsetter::subset($this->fontData, $this->usedGlyphs);
            if ($sub !== null) {
                $program = $sub;
                $tagged  = $this->subsetTag() . '+' . $this->baseFont;
            }
        }
        $ff = new PdfStream();
        $ff->getDictionary()->set('Length1', new PdfInteger(strlen($program)));
        $ff->setData($program);
        $ff->compress();
        $ffNum = $allocate($ff);

        // 2. FontDescriptor.
        $desc = new FontDescriptor($tagged);
        $desc->setFlags(FontDescriptor::FLAG_NONSYMBOLIC)
            ->setItalicAngle($this->metrics['italicAngle'])
            ->setAscent($this->metrics['ascent'])
            ->setDescent($this->metrics['descent'])
            ->setCapHeight($this->metrics['capHeight'])
            ->setStemV(80)
            ->setFontBBox(...$this->metrics['bbox']);
        $desc->setFontFile2(new PdfIndirectReference($ffNum));
        $descNum = $allocate($desc->toDictionary());

        // 3. Descendant CIDFontType2 (Identity CIDToGIDMap → CID == GID).
        $cid = new PdfDictionary();
        $cid->set('Type', new PdfName('Font'));
        $cid->set('Subtype', new PdfName('CIDFontType2'));
        $cid->set('BaseFont', new PdfName($tagged));
        $sysInfo = new PdfDictionary();
        $sysInfo->set('Registry', new PdfString('Adobe'));
        $sysInfo->set('Ordering', new PdfString('Identity'));
        $sysInfo->set('Supplement', new PdfInteger(0));
        $cid->set('CIDSystemInfo', $sysInfo);
        $cid->set('FontDescriptor', new PdfIndirectReference($descNum));
        $cid->set('CIDToGIDMap', new PdfName('Identity'));
        $cid->set('DW', new PdfInteger(1000));
        $cid->set('W', $this->buildWidthsArray());
        $cidNum = $allocate($cid);
        $this->descendantRef = new PdfIndirectReference($cidNum);

        // 4. ToUnicode CMap.
        $toUni = $this->buildToUnicodeStream();
        if ($toUni !== null) {
            $this->toUnicodeRef = new PdfIndirectReference($allocate($toUni));
        }

        // 5. Update BaseFont with the subset tag.
        $this->baseFont = $tagged;
        $this->dictionary->set('BaseFont', new PdfName($tagged));
    }

    /** Build the CIDFont /W array (CID → advance width) for used glyphs. */
    private function buildWidthsArray(): PdfArray
    {
        $gids = array_keys($this->usedGlyphs);
        sort($gids);
        $w = new PdfArray();
        foreach ($gids as $gid) {
            $width = $this->advances[$gid] ?? 1000;
            if ($width === 1000) {
                continue; // covered by DW
            }
            $w->add(new PdfInteger($gid));
            $inner = new PdfArray();
            $inner->add(new PdfInteger($width));
            $w->add($inner);
        }
        return $w;
    }

    /** Build a /ToUnicode CMap mapping two-byte glyph codes back to Unicode. */
    private function buildToUnicodeStream(): ?PdfStream
    {
        // Invert the cmap (glyph id → first Unicode code point that maps to it).
        $gidToUni = [];
        foreach ($this->cmap as $cp => $gid) {
            if (isset($this->usedGlyphs[$gid]) && !isset($gidToUni[$gid])) {
                $gidToUni[$gid] = $cp;
            }
        }
        if (empty($gidToUni)) {
            return null;
        }
        ksort($gidToUni);

        $entries = [];
        foreach ($gidToUni as $gid => $cp) {
            $src = sprintf('%04X', $gid);
            $dst = strtoupper(bin2hex(mb_convert_encoding(mb_chr($cp, 'UTF-8'), 'UTF-16BE', 'UTF-8')));
            $entries[] = "<$src> <$dst>";
        }

        $cmap  = "/CIDInit /ProcSet findresource begin\n12 dict begin\nbegincmap\n";
        $cmap .= "/CIDSystemInfo << /Registry (Adobe) /Ordering (UCS) /Supplement 0 >> def\n";
        $cmap .= "/CMapName /Adobe-Identity-UCS def\n/CMapType 2 def\n";
        $cmap .= "1 begincodespacerange\n<0000> <FFFF>\nendcodespacerange\n";
        foreach (array_chunk($entries, 100) as $chunk) {
            $cmap .= count($chunk) . " beginbfchar\n" . implode("\n", $chunk) . "\nendbfchar\n";
        }
        $cmap .= "endcmap\nCMapName currentdict /CMap defineresource pop\nend\nend\n";

        $stream = new PdfStream();
        $stream->setData($cmap);
        $stream->compress();
        return $stream;
    }

    private function subsetTag(): string
    {
        $hash = md5($this->baseFont . ':' . implode(',', array_keys($this->usedGlyphs)));
        $tag = '';
        for ($i = 0; $i < 6; $i++) {
            $tag .= chr(65 + (hexdec($hash[$i]) % 26));
        }
        return $tag;
    }
}
