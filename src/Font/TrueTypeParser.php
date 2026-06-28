<?php

declare(strict_types=1);

namespace Papier\Font;

/**
 * Minimal TrueType/OpenType table reader (ISO 32000-1 §9.6.3 / OpenType spec).
 *
 * Extracts the metrics and mappings needed to embed a font as a composite
 * (Type 0 / CIDFontType2) program: the Unicode→glyph map, per-glyph advance
 * widths, and the global font metrics for the descriptor.
 */
final class TrueTypeParser
{
    /**
     * @return array{
     *   unitsPerEm:int, ascent:int, descent:int, capHeight:int, italicAngle:int,
     *   numGlyphs:int, bbox:array{0:int,1:int,2:int,3:int},
     *   cmap:array<int,int>, advances:array<int,int>, postScriptName:string, isCFF:bool
     * }
     */
    public static function parse(string $data): array
    {
        $tables = self::directory($data);
        $isCFF  = substr($data, 0, 4) === 'OTTO';

        $unitsPerEm = 1000; $ascent = 800; $descent = -200; $capHeight = 700;
        $italicAngle = 0; $numGlyphs = 0; $numHMetrics = 0;
        $bbox = [0, -200, 1000, 800];

        if (isset($tables['head'])) {
            $o = $tables['head']['offset'];
            $unitsPerEm = self::u16($data, $o + 18) ?: 1000;
            $bbox = [
                self::s16($data, $o + 36), self::s16($data, $o + 38),
                self::s16($data, $o + 40), self::s16($data, $o + 42),
            ];
        }
        if (isset($tables['hhea'])) {
            $o = $tables['hhea']['offset'];
            $ascent      = self::s16($data, $o + 4);
            $descent     = self::s16($data, $o + 6);
            $numHMetrics = self::u16($data, $o + 34);
        }
        if (isset($tables['maxp'])) {
            $numGlyphs = self::u16($data, $tables['maxp']['offset'] + 4);
        }
        if (isset($tables['post'])) {
            $italicAngle = (int) round(self::fixed($data, $tables['post']['offset'] + 4));
        }
        if (isset($tables['OS/2'])) {
            $o = $tables['OS/2']['offset'];
            if (self::u16($data, $o) >= 2) {
                $capHeight = self::s16($data, $o + 88);
            }
        }
        if ($capHeight === 0) {
            $capHeight = (int) round($ascent * 0.7);
        }

        $cmap     = isset($tables['cmap']) ? self::parseCmap($data, $tables['cmap']['offset']) : [];
        $advances = (isset($tables['hmtx']) && $numHMetrics > 0)
            ? self::parseHmtx($data, $tables['hmtx']['offset'], $numHMetrics, $numGlyphs)
            : [];

        $scale = static fn(int $v): int => (int) round($v * 1000 / $unitsPerEm);

        return [
            'unitsPerEm'     => $unitsPerEm,
            'ascent'         => $scale($ascent),
            'descent'        => $scale($descent),
            'capHeight'      => $scale($capHeight),
            'italicAngle'    => $italicAngle,
            'numGlyphs'      => $numGlyphs,
            'bbox'           => [$scale($bbox[0]), $scale($bbox[1]), $scale($bbox[2]), $scale($bbox[3])],
            'cmap'           => $cmap,
            // Normalise advances to 1000 units/em.
            'advances'       => array_map($scale, $advances),
            'postScriptName' => self::postScriptName($data, $tables),
            'isCFF'          => $isCFF,
        ];
    }

    /** @return array<string,array{offset:int,length:int}> */
    private static function directory(string $data): array
    {
        if (strlen($data) < 12) {
            return [];
        }
        $num = self::u16($data, 4);
        $out = [];
        for ($i = 0; $i < $num; $i++) {
            $rec = 12 + $i * 16;
            $out[substr($data, $rec, 4)] = [
                'offset' => self::u32($data, $rec + 8),
                'length' => self::u32($data, $rec + 12),
            ];
        }
        return $out;
    }

    /** @return array<int,int> glyphId → advance width (font units) */
    private static function parseHmtx(string $data, int $off, int $numHMetrics, int $numGlyphs): array
    {
        $adv = [];
        $last = 0;
        for ($i = 0; $i < $numHMetrics; $i++) {
            $last = self::u16($data, $off + $i * 4);
            $adv[$i] = $last;
        }
        // Glyphs beyond numHMetrics share the last advance width.
        for ($i = $numHMetrics; $i < $numGlyphs; $i++) {
            $adv[$i] = $last;
        }
        return $adv;
    }

    /** @return array<int,int> Unicode code point → glyph id (format 4 + 12) */
    private static function parseCmap(string $data, int $cmapOff): array
    {
        $num = self::u16($data, $cmapOff + 2);
        $best = null; $bestScore = -1;
        for ($i = 0; $i < $num; $i++) {
            $rec  = $cmapOff + 4 + $i * 8;
            $plat = self::u16($data, $rec);
            $enc  = self::u16($data, $rec + 2);
            $sub  = $cmapOff + self::u32($data, $rec + 4);
            // Prefer Windows full-repertoire (3,10), then Windows BMP (3,1), then Unicode.
            $score = match (true) {
                $plat === 3 && $enc === 10 => 4,
                $plat === 0               => 3,
                $plat === 3 && $enc === 1  => 2,
                default                    => 1,
            };
            if ($score > $bestScore) { $bestScore = $score; $best = $sub; }
        }
        if ($best === null) {
            return [];
        }
        $format = self::u16($data, $best);
        return match ($format) {
            4       => self::cmapFormat4($data, $best),
            12      => self::cmapFormat12($data, $best),
            default => [],
        };
    }

    /** @return array<int,int> */
    private static function cmapFormat4(string $data, int $off): array
    {
        $segX2 = self::u16($data, $off + 6);
        $seg   = intdiv($segX2, 2);
        $endO   = $off + 14;
        $startO = $endO + $segX2 + 2;
        $deltaO = $startO + $segX2;
        $rangeO = $deltaO + $segX2;

        $map = [];
        for ($i = 0; $i < $seg; $i++) {
            $end   = self::u16($data, $endO + $i * 2);
            $start = self::u16($data, $startO + $i * 2);
            $delta = self::u16($data, $deltaO + $i * 2);
            $range = self::u16($data, $rangeO + $i * 2);
            if ($start === 0xFFFF) { break; }
            for ($c = $start; $c <= $end; $c++) {
                if ($range === 0) {
                    $g = ($c + $delta) & 0xFFFF;
                } else {
                    $gi = $rangeO + $i * 2 + $range + ($c - $start) * 2;
                    if ($gi + 1 >= strlen($data)) { continue; }
                    $g = self::u16($data, $gi);
                    if ($g !== 0) { $g = ($g + $delta) & 0xFFFF; }
                }
                if ($g !== 0) { $map[$c] = $g; }
            }
        }
        return $map;
    }

    /** @return array<int,int> */
    private static function cmapFormat12(string $data, int $off): array
    {
        $nGroups = self::u32($data, $off + 12);
        $map = [];
        for ($i = 0; $i < $nGroups; $i++) {
            $g = $off + 16 + $i * 12;
            $startChar = self::u32($data, $g);
            $endChar   = self::u32($data, $g + 4);
            $startGid  = self::u32($data, $g + 8);
            // Guard against pathological group sizes.
            if ($endChar - $startChar > 65535) { $endChar = $startChar + 65535; }
            for ($c = $startChar; $c <= $endChar; $c++) {
                $map[$c] = $startGid + ($c - $startChar);
            }
        }
        return $map;
    }

    private static function postScriptName(string $data, array $tables): string
    {
        if (!isset($tables['name'])) { return ''; }
        $o     = $tables['name']['offset'];
        $count = self::u16($data, $o + 2);
        $strBase = $o + self::u16($data, $o + 4);
        $fallback = '';
        for ($i = 0; $i < $count; $i++) {
            $r    = $o + 6 + $i * 12;
            $plat = self::u16($data, $r);
            $enc  = self::u16($data, $r + 2);
            $nid  = self::u16($data, $r + 6);
            $len  = self::u16($data, $r + 8);
            $so   = self::u16($data, $r + 10);
            if ($nid !== 6) { continue; }
            $raw = substr($data, $strBase + $so, $len);
            if ($plat === 3 && $enc === 1) {
                return (string) mb_convert_encoding($raw, 'UTF-8', 'UTF-16BE');
            }
            if ($plat === 1 && $enc === 0) { $fallback = $raw; }
        }
        return $fallback;
    }

    private static function u16(string $d, int $o): int
    {
        if ($o + 1 >= strlen($d)) { return 0; }
        return ((ord($d[$o]) << 8) | ord($d[$o + 1])) & 0xFFFF;
    }

    private static function s16(string $d, int $o): int
    {
        $v = self::u16($d, $o);
        return $v >= 0x8000 ? $v - 0x10000 : $v;
    }

    private static function u32(string $d, int $o): int
    {
        if ($o + 3 >= strlen($d)) { return 0; }
        return ((ord($d[$o]) << 24) | (ord($d[$o + 1]) << 16) | (ord($d[$o + 2]) << 8) | ord($d[$o + 3])) & 0xFFFFFFFF;
    }

    private static function fixed(string $d, int $o): float
    {
        $v = self::u32($d, $o);
        if ($v >= 0x80000000) { $v -= 0x100000000; }
        return $v / 65536.0;
    }
}
