<?php

declare(strict_types=1);

namespace Papier\Font;

/**
 * TrueType font subsetter (ISO 32000-1 §9.9 / OpenType `glyf` outline format).
 *
 * Strips the outlines of glyphs that are not referenced, keeping the original
 * glyph numbering intact.  Because glyph ids, `cmap`, `hmtx`, and `maxp` are
 * unchanged, the resulting font program remains structurally valid for use as a
 * simple (non-composite) TrueType font while shedding the bulk of the `glyf`
 * table — the dominant contributor to file size in large fonts.
 *
 * Composite glyphs that are kept pull in their component glyphs automatically.
 *
 * Returns null when the font cannot be subset this way (e.g. CFF/OpenType
 * fonts that have no `glyf` table); callers should embed the full program.
 */
final class TrueTypeSubsetter
{
    // Composite glyph component flags (OpenType `glyf`).
    private const ARG_1_AND_2_ARE_WORDS   = 0x0001;
    private const WE_HAVE_A_SCALE         = 0x0008;
    private const MORE_COMPONENTS         = 0x0020;
    private const WE_HAVE_AN_X_AND_Y_SCALE= 0x0040;
    private const WE_HAVE_A_TWO_BY_TWO    = 0x0080;

    /**
     * @param string          $data        Original font program bytes.
     * @param array<int,true> $usedGlyphs  Glyph ids to keep (glyph 0 is always kept).
     */
    public static function subset(string $data, array $usedGlyphs): ?string
    {
        if (strlen($data) < 12) {
            return null;
        }
        $sfntVersion = substr($data, 0, 4);
        // CFF-flavoured OpenType has no `glyf` table; cannot subset here.
        if ($sfntVersion === 'OTTO') {
            return null;
        }

        $tables = self::readDirectory($data);
        foreach (['head', 'maxp', 'loca', 'glyf'] as $req) {
            if (!isset($tables[$req])) {
                return null;
            }
        }

        $head = substr($data, $tables['head']['offset'], $tables['head']['length']);
        $maxp = substr($data, $tables['maxp']['offset'], $tables['maxp']['length']);

        $indexToLocFormat = self::u16($head, 50);
        $numGlyphs        = self::u16($maxp, 4);
        if ($numGlyphs <= 0) {
            return null;
        }

        // Read the loca table → glyph byte ranges within glyf.
        $loca = self::readLoca($data, $tables['loca'], $indexToLocFormat, $numGlyphs);
        if ($loca === null) {
            return null;
        }
        $glyfOffset = $tables['glyf']['offset'];
        $glyfData   = substr($data, $glyfOffset, $tables['glyf']['length']);

        // Resolve the full set of glyphs to keep (composites pull in components).
        $keep = [0 => true] + $usedGlyphs;
        $keep = self::expandComposites($glyfData, $loca, $keep, $numGlyphs);

        // Build the new glyf + loca (long format) preserving glyph ids.
        $newGlyf = '';
        $newLoca = [];
        for ($gid = 0; $gid < $numGlyphs; $gid++) {
            $newLoca[$gid] = strlen($newGlyf);
            if (isset($keep[$gid])) {
                $start = $loca[$gid];
                $end   = $loca[$gid + 1];
                if ($end > $start) {
                    $chunk = substr($glyfData, $start, $end - $start);
                    // Pad each glyph to a 4-byte boundary.
                    $newGlyf .= $chunk;
                    while (strlen($newGlyf) % 4 !== 0) {
                        $newGlyf .= "\x00";
                    }
                }
            }
        }
        $newLoca[$numGlyphs] = strlen($newGlyf);

        // Encode loca in long format (uint32) and force head.indexToLocFormat = 1.
        $locaData = '';
        foreach ($newLoca as $off) {
            $locaData .= pack('N', $off);
        }
        $head = substr_replace($head, pack('n', 1), 50, 2);
        // Zero head.checkSumAdjustment before recomputing (offset 8, 4 bytes).
        $head = substr_replace($head, "\x00\x00\x00\x00", 8, 4);

        // Assemble the new font with replaced tables.
        $replacements = [
            'head' => $head,
            'loca' => $locaData,
            'glyf' => $newGlyf,
        ];

        return self::assemble($data, $tables, $replacements, $sfntVersion);
    }

    /** @return array<string, array{offset:int, length:int}> */
    private static function readDirectory(string $data): array
    {
        $numTables = self::u16($data, 4);
        $tables = [];
        for ($i = 0; $i < $numTables; $i++) {
            $rec = 12 + $i * 16;
            $tag = substr($data, $rec, 4);
            $tables[$tag] = [
                'offset' => self::u32($data, $rec + 8),
                'length' => self::u32($data, $rec + 12),
            ];
        }
        return $tables;
    }

    /**
     * @param array{offset:int,length:int} $loca
     * @return array<int,int>|null  numGlyphs+1 byte offsets into glyf
     */
    private static function readLoca(string $data, array $loca, int $format, int $numGlyphs): ?array
    {
        $offsets = [];
        $base    = $loca['offset'];
        $needed  = ($numGlyphs + 1) * ($format === 0 ? 2 : 4);
        if ($loca['length'] < $needed) {
            return null; // truncated/invalid loca table
        }
        if ($format === 0) {
            for ($i = 0; $i <= $numGlyphs; $i++) {
                $offsets[$i] = self::u16($data, $base + $i * 2) * 2;
            }
        } else {
            for ($i = 0; $i <= $numGlyphs; $i++) {
                $offsets[$i] = self::u32($data, $base + $i * 4);
            }
        }
        return $offsets;
    }

    /**
     * Expand the keep-set to include components referenced by kept composite glyphs.
     *
     * @param array<int,int>  $loca
     * @param array<int,true> $keep
     * @return array<int,true>
     */
    private static function expandComposites(string $glyf, array $loca, array $keep, int $numGlyphs): array
    {
        $stack = array_keys($keep);
        while (!empty($stack)) {
            $gid = array_pop($stack);
            if ($gid < 0 || $gid >= $numGlyphs) {
                continue;
            }
            $start = $loca[$gid];
            $end   = $loca[$gid + 1];
            if ($end - $start < 10) {
                continue; // empty or simple glyph too short to be composite
            }
            $numberOfContours = self::s16($glyf, $start);
            if ($numberOfContours >= 0) {
                continue; // simple glyph, no components
            }

            // Walk the composite components.
            $pos = $start + 10;
            do {
                if ($pos + 4 > $end) {
                    break;
                }
                $flags      = self::u16($glyf, $pos);
                $compGlyph  = self::u16($glyf, $pos + 2);
                $pos += 4;

                if (!isset($keep[$compGlyph])) {
                    $keep[$compGlyph] = true;
                    $stack[] = $compGlyph;
                }

                $pos += ($flags & self::ARG_1_AND_2_ARE_WORDS) ? 4 : 2;
                if ($flags & self::WE_HAVE_A_SCALE) {
                    $pos += 2;
                } elseif ($flags & self::WE_HAVE_AN_X_AND_Y_SCALE) {
                    $pos += 4;
                } elseif ($flags & self::WE_HAVE_A_TWO_BY_TWO) {
                    $pos += 8;
                }
            } while ($flags & self::MORE_COMPONENTS);
        }
        return $keep;
    }

    /**
     * Reassemble the sfnt with replacement table data, recomputing checksums.
     *
     * @param array<string,array{offset:int,length:int}> $tables
     * @param array<string,string>                       $replacements
     */
    private static function assemble(string $data, array $tables, array $replacements, string $sfntVersion): string
    {
        // Gather final table data (sorted by tag for a canonical layout).
        $final = [];
        foreach ($tables as $tag => $info) {
            $final[$tag] = $replacements[$tag]
                ?? substr($data, $info['offset'], $info['length']);
        }
        ksort($final);

        $numTables     = count($final);
        $searchRange   = (2 ** (int) floor(log($numTables, 2))) * 16;
        $entrySelector = (int) floor(log($numTables, 2));
        $rangeShift    = $numTables * 16 - $searchRange;

        $header  = $sfntVersion;
        $header .= pack('n', $numTables);
        $header .= pack('n', $searchRange);
        $header .= pack('n', $entrySelector);
        $header .= pack('n', $rangeShift);

        $dirSize    = 16 * $numTables;
        $offset     = 12 + $dirSize;
        $directory  = '';
        $body       = '';
        $headBodyPos = null;

        foreach ($final as $tag => $tdata) {
            $length   = strlen($tdata);
            $checksum = self::checksum($tdata);
            $padded   = $tdata;
            while (strlen($padded) % 4 !== 0) {
                $padded .= "\x00";
            }

            $directory .= $tag . pack('N', $checksum) . pack('N', $offset) . pack('N', $length);

            if ($tag === 'head') {
                $headBodyPos = strlen($body); // position within body
            }
            $body   .= $padded;
            $offset += strlen($padded);
        }

        $font = $header . $directory . $body;

        // head.checkSumAdjustment = 0xB1B0AFBA − checksum(whole file).
        if ($headBodyPos !== null) {
            $adjustment = (0xB1B0AFBA - self::checksum($font)) & 0xFFFFFFFF;
            $headFilePos = 12 + $dirSize + $headBodyPos + 8;
            $font = substr_replace($font, pack('N', $adjustment), $headFilePos, 4);
        }

        return $font;
    }

    private static function checksum(string $data): int
    {
        while (strlen($data) % 4 !== 0) {
            $data .= "\x00";
        }
        $sum = 0;
        $n   = strlen($data);
        for ($i = 0; $i < $n; $i += 4) {
            $sum = ($sum + self::u32($data, $i)) & 0xFFFFFFFF;
        }
        return $sum;
    }

    private static function u16(string $d, int $o): int
    {
        return ((ord($d[$o]) << 8) | ord($d[$o + 1])) & 0xFFFF;
    }

    private static function s16(string $d, int $o): int
    {
        $v = self::u16($d, $o);
        return $v >= 0x8000 ? $v - 0x10000 : $v;
    }

    private static function u32(string $d, int $o): int
    {
        return ((ord($d[$o]) << 24) | (ord($d[$o + 1]) << 16) | (ord($d[$o + 2]) << 8) | ord($d[$o + 3])) & 0xFFFFFFFF;
    }
}
