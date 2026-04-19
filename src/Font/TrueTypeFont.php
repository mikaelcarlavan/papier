<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Font\Encoding\Encoding;
use Papier\Font\Encoding\WinAnsiEncoding;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfStream, PdfString};

/**
 * TrueType font (ISO 32000-1 §9.6.3).
 *
 * Supports embedding of TrueType/OpenType font programs (FontFile2).
 * For CJK text use Type0 (composite) with a CIDFontType2 descendant instead.
 */
final class TrueTypeFont extends Font
{
    private Encoding        $encoding;
    private ?FontDescriptor $descriptor   = null;
    private ?PdfObject      $widthsArray  = null;
    private int             $firstChar    = 32;
    private int             $lastChar     = 255;
    private ?string         $fontData     = null; // raw TrueType binary
    /** @var array<int, int> charCode → glyph width in 1/1000 units */
    private array           $glyphWidths  = [];

    public function __construct(
        private readonly string $baseFont,
        ?Encoding $encoding = null,
    ) {
        parent::__construct();
        $this->encoding = $encoding ?? new WinAnsiEncoding();
        $this->dictionary->set('Subtype', new PdfName('TrueType'));
        $this->dictionary->set('BaseFont', new PdfName($baseFont));
    }

    public function getSubtype(): string { return 'TrueType'; }

    public function setEncoding(Encoding $encoding): static
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function setFontDescriptor(FontDescriptor $descriptor): static
    {
        $this->descriptor = $descriptor;
        return $this;
    }

    /**
     * Load a TrueType font file for embedding.
     *
     * Parses the 'hhea', 'OS/2', and 'hmtx' tables to extract metrics and
     * glyph widths automatically.
     */
    public function loadFromFile(string $path): static
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Font file not found: $path");
        }
        $this->fontData = file_get_contents($path);
        if ($this->fontData === false) {
            throw new \RuntimeException("Cannot read font file: $path");
        }
        $this->parseTrueTypeMetrics();
        return $this;
    }

    /** Load from binary string (e.g., from a database or embedded resource). */
    public function loadFromData(string $data): static
    {
        $this->fontData = $data;
        $this->parseTrueTypeMetrics();
        return $this;
    }

    /**
     * Create a TrueTypeFont by loading a TTF or OTF file.
     *
     * The PostScript name is extracted automatically from the font's `name`
     * table (nameID 6).  If not found, the filename (without extension) is
     * used as a fallback.
     *
     * @param string $path  Absolute or relative path to a `.ttf` or `.otf` file.
     *
     * @throws \InvalidArgumentException  If the file cannot be read.
     */
    public static function fromFile(string $path): self
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Font file not found: $path");
        }
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \RuntimeException("Cannot read font file: $path");
        }

        $psName = self::extractPostScriptName($data)
            ?? pathinfo($path, PATHINFO_FILENAME);

        // Sanitise: PDF /Name tokens may not contain whitespace or special chars
        $psName = preg_replace('/[^\x21-\x7E]/', '-', $psName);

        $font = new self($psName);
        $font->fontData = $data;
        $font->parseTrueTypeMetrics();
        return $font;
    }

    /**
     * Extract the PostScript name (nameID 6) from the font binary's `name` table.
     * Returns null if the table or record is absent.
     */
    private static function extractPostScriptName(string $data): ?string
    {
        if (strlen($data) < 12) {
            return null;
        }

        $numTables = unpack('n', substr($data, 4, 2))[1] ?? 0;
        $nameOffset = null;
        for ($i = 0; $i < $numTables; $i++) {
            $base = 12 + $i * 16;
            if (substr($data, $base, 4) === 'name') {
                $nameOffset = unpack('N', substr($data, $base + 8, 4))[1];
                break;
            }
        }
        if ($nameOffset === null) {
            return null;
        }

        $count      = unpack('n', substr($data, $nameOffset + 2, 2))[1] ?? 0;
        $stringBase = $nameOffset + (unpack('n', substr($data, $nameOffset + 4, 2))[1] ?? 0);

        $fallback = null;
        for ($i = 0; $i < $count; $i++) {
            $r          = $nameOffset + 6 + $i * 12;
            $platformId = unpack('n', substr($data, $r,     2))[1];
            $encodingId = unpack('n', substr($data, $r + 2, 2))[1];
            $nameId     = unpack('n', substr($data, $r + 6, 2))[1];
            $length     = unpack('n', substr($data, $r + 8, 2))[1];
            $strOffset  = unpack('n', substr($data, $r + 10, 2))[1];

            if ($nameId !== 6) {
                continue;
            }

            $raw = substr($data, $stringBase + $strOffset, $length);

            // Windows Unicode (3,1) → UTF-16BE
            if ($platformId === 3 && $encodingId === 1) {
                return mb_convert_encoding($raw, 'UTF-8', 'UTF-16BE');
            }
            // Mac Roman (1,0) → ASCII
            if ($platformId === 1 && $encodingId === 0) {
                $fallback = $raw;
            }
        }
        return $fallback;
    }

    public function stringWidth(string $text, float $size): float
    {
        $width = 0;
        $len   = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $code  = ord($text[$i]);
            $width += $this->glyphWidths[$code] ?? 500;
        }
        return $width * $size / 1000;
    }

    public function getDictionary(): PdfDictionary
    {
        $this->dictionary->set('Encoding', $this->encoding->toPdfObject());
        if ($this->widthsArray !== null) {
            $this->dictionary->set('FirstChar', new PdfInteger($this->firstChar));
            $this->dictionary->set('LastChar', new PdfInteger($this->lastChar));
            $this->dictionary->set('Widths', $this->widthsArray);
        }
        return $this->dictionary;
    }

    /**
     * Build the FontFile2 stream to be registered as an indirect object.
     */
    public function buildFontFileStream(): ?PdfStream
    {
        if ($this->fontData === null) {
            return null;
        }
        $stream = new PdfStream();
        $stream->getDictionary()->set('Length1', new PdfInteger(strlen($this->fontData)));
        $stream->setData($this->fontData);
        $stream->compress();
        return $stream;
    }

    // ── TrueType table parsing ─────────────────────────────────────────────────

    private function parseTrueTypeMetrics(): void
    {
        $data = $this->fontData;
        if (strlen($data) < 12) {
            return;
        }

        // Read offset table
        $numTables = unpack('n', substr($data, 4, 2))[1] ?? 0;

        // Build table directory
        $tables = [];
        $offset = 12;
        for ($i = 0; $i < $numTables; $i++) {
            $tag      = substr($data, $offset, 4);
            $checksum = unpack('N', substr($data, $offset + 4, 4))[1];
            $tOffset  = unpack('N', substr($data, $offset + 8, 4))[1];
            $length   = unpack('N', substr($data, $offset + 12, 4))[1];
            $tables[$tag] = ['offset' => $tOffset, 'length' => $length];
            $offset += 16;
        }

        $unitsPerEm = 1000;
        $ascender   = 800;
        $descender  = -200;
        $capHeight  = 700;
        $stemV      = 80;

        // Parse 'head' table for unitsPerEm
        if (isset($tables['head'])) {
            $headOff = $tables['head']['offset'];
            $unitsPerEm = unpack('n', substr($data, $headOff + 18, 2))[1] ?? 1000;
        }

        // Parse 'hhea' for ascender/descender
        if (isset($tables['hhea'])) {
            $hheaOff  = $tables['hhea']['offset'];
            $ascender  = self::signedShort(unpack('n', substr($data, $hheaOff + 4, 2))[1] ?? 0);
            $descender = self::signedShort(unpack('n', substr($data, $hheaOff + 6, 2))[1] ?? 0);
            $numHMetrics = unpack('n', substr($data, $hheaOff + 34, 2))[1] ?? 0;
        }

        // Parse 'OS/2' for capHeight, stemV, etc.
        if (isset($tables['OS/2'])) {
            $os2Off   = $tables['OS/2']['offset'];
            $version  = unpack('n', substr($data, $os2Off, 2))[1] ?? 0;
            if ($version >= 2) {
                $capHeight = self::signedShort(unpack('n', substr($data, $os2Off + 88, 2))[1] ?? 0);
                if ($capHeight === 0) {
                    $capHeight = (int) ($ascender * 700 / ($unitsPerEm ?: 1000));
                }
            }
        }

        // Parse 'hmtx' for glyph widths (advance widths)
        if (isset($tables['hmtx'], $tables['cmap'])) {
            $hmtxOff   = $tables['hmtx']['offset'];
            $cmapOff   = $tables['cmap']['offset'];
            $numHMetrics = $numHMetrics ?? 1;

            // Build codepoint → glyphId map from 'cmap' (prefer format 4)
            $cpToGlyph = $this->parseCmapFormat4($data, $cmapOff);

            // Build glyphId → advance width from 'hmtx'
            $glyphWidthFn = function (int $glyphId) use ($data, $hmtxOff, $numHMetrics): int {
                if ($glyphId < $numHMetrics) {
                    $idx = $hmtxOff + $glyphId * 4;
                } else {
                    $idx = $hmtxOff + ($numHMetrics - 1) * 4;
                }
                return unpack('n', substr($data, $idx, 2))[1] ?? 500;
            };

            // Map Windows-1252 code points to widths
            $widths = [];
            for ($cp = 32; $cp <= 255; $cp++) {
                // Convert cp1252 → Unicode codepoint (approximate)
                $unicode  = $this->cp1252ToUnicode($cp);
                $glyphId  = $cpToGlyph[$unicode] ?? 0;
                $advance  = $glyphWidthFn($glyphId);
                // Normalise to 1000 units
                $widths[$cp] = (int) round($advance * 1000 / ($unitsPerEm ?: 1000));
                $this->glyphWidths[$cp] = $widths[$cp];
            }

            $this->firstChar = 32;
            $this->lastChar  = 255;
            $arr = new PdfArray();
            for ($cp = 32; $cp <= 255; $cp++) {
                $arr->add(new PdfInteger($widths[$cp] ?? 500));
            }
            $this->widthsArray = $arr;
        }

        // Build FontDescriptor
        $desc = new FontDescriptor($this->baseFont);
        $scale = 1000 / ($unitsPerEm ?: 1000);
        $desc
            ->setFlags(FontDescriptor::FLAG_NONSYMBOLIC)
            ->setAscent((int) round($ascender * $scale))
            ->setDescent((int) round($descender * $scale))
            ->setCapHeight((int) round($capHeight * $scale))
            ->setStemV($stemV)
            ->setFontBBox(
                0,
                (int) round($descender * $scale),
                1000,
                (int) round($ascender * $scale)
            );
        $this->descriptor = $desc;
    }

    private function parseCmapFormat4(string $data, int $cmapOff): array
    {
        $numSubtables = unpack('n', substr($data, $cmapOff + 2, 2))[1] ?? 0;
        $subtableOff  = null;

        for ($i = 0; $i < $numSubtables; $i++) {
            $base       = $cmapOff + 4 + $i * 8;
            $platformId = unpack('n', substr($data, $base, 2))[1];
            $encodingId = unpack('n', substr($data, $base + 2, 2))[1];
            $off        = unpack('N', substr($data, $base + 4, 4))[1];

            // Prefer Windows Unicode BMP (3, 1)
            if ($platformId === 3 && $encodingId === 1) {
                $subtableOff = $cmapOff + $off;
                break;
            }
            // Fall back to Mac Roman (1, 0)
            if ($platformId === 1 && $encodingId === 0 && $subtableOff === null) {
                $subtableOff = $cmapOff + $off;
            }
        }

        if ($subtableOff === null) {
            return [];
        }

        $format = unpack('n', substr($data, $subtableOff, 2))[1];
        if ($format !== 4) {
            return [];
        }

        $segCount = unpack('n', substr($data, $subtableOff + 6, 2))[1] / 2;
        $map      = [];
        $endOff   = $subtableOff + 14;
        $startOff = $endOff + $segCount * 2 + 2;
        $deltaOff = $startOff + $segCount * 2;
        $rangeOff = $deltaOff + $segCount * 2;

        for ($i = 0; $i < $segCount; $i++) {
            $endCode   = unpack('n', substr($data, $endOff + $i * 2, 2))[1];
            $startCode = unpack('n', substr($data, $startOff + $i * 2, 2))[1];
            $delta     = self::signedShort(unpack('n', substr($data, $deltaOff + $i * 2, 2))[1]);
            $range     = unpack('n', substr($data, $rangeOff + $i * 2, 2))[1];

            if ($startCode === 0xFFFF) {
                break;
            }

            for ($cp = $startCode; $cp <= $endCode; $cp++) {
                if ($range === 0) {
                    $glyphId = ($cp + $delta) & 0xFFFF;
                } else {
                    $rangePtr = $rangeOff + $i * 2 + $range + ($cp - $startCode) * 2;
                    $glyphId  = unpack('n', substr($data, $rangePtr, 2))[1];
                    if ($glyphId !== 0) {
                        $glyphId = ($glyphId + $delta) & 0xFFFF;
                    }
                }
                $map[$cp] = $glyphId;
            }
        }
        return $map;
    }

    private function cp1252ToUnicode(int $cp): int
    {
        // Windows-1252 → Unicode for 0x80–0x9F
        $ext = [
            0x80 => 0x20AC, 0x82 => 0x201A, 0x83 => 0x0192, 0x84 => 0x201E,
            0x85 => 0x2026, 0x86 => 0x2020, 0x87 => 0x2021, 0x88 => 0x02C6,
            0x89 => 0x2030, 0x8A => 0x0160, 0x8B => 0x2039, 0x8C => 0x0152,
            0x8E => 0x017D, 0x91 => 0x2018, 0x92 => 0x2019, 0x93 => 0x201C,
            0x94 => 0x201D, 0x95 => 0x2022, 0x96 => 0x2013, 0x97 => 0x2014,
            0x98 => 0x02DC, 0x99 => 0x2122, 0x9A => 0x0161, 0x9B => 0x203A,
            0x9C => 0x0153, 0x9E => 0x017E, 0x9F => 0x0178,
        ];
        return $ext[$cp] ?? $cp;
    }

    private static function signedShort(int $v): int
    {
        return $v >= 0x8000 ? $v - 0x10000 : $v;
    }

    public function getDescriptor(): ?FontDescriptor { return $this->descriptor; }
}
