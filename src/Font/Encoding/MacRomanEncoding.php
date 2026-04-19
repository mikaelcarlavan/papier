<?php

declare(strict_types=1);

namespace Papier\Font\Encoding;

/**
 * MacRomanEncoding (ISO 32000-1 Annex D.1).
 */
final class MacRomanEncoding extends Encoding
{
    private const TABLE = [
        0x80 => 'Adieresis',  0x81 => 'Aring',      0x82 => 'Ccedilla',
        0x83 => 'Eacute',     0x84 => 'Ntilde',      0x85 => 'Odieresis',
        0x86 => 'Udieresis',  0x87 => 'aacute',      0x88 => 'agrave',
        0x89 => 'acircumflex',0x8A => 'adieresis',   0x8B => 'atilde',
        0x8C => 'aring',      0x8D => 'ccedilla',    0x8E => 'eacute',
        0x8F => 'egrave',     0x90 => 'ecircumflex', 0x91 => 'edieresis',
        0x92 => 'iacute',     0x93 => 'igrave',      0x94 => 'icircumflex',
        0x95 => 'idieresis',  0x96 => 'ntilde',      0x97 => 'oacute',
        0x98 => 'ograve',     0x99 => 'ocircumflex', 0x9A => 'odieresis',
        0x9B => 'otilde',     0x9C => 'uacute',      0x9D => 'ugrave',
        0x9E => 'ucircumflex',0x9F => 'udieresis',
        0xA0 => 'dagger',     0xA1 => 'degree',      0xA2 => 'cent',
        0xA3 => 'sterling',   0xA4 => 'section',     0xA5 => 'bullet',
        0xA6 => 'paragraph',  0xA7 => 'germandbls',  0xA8 => 'registered',
        0xA9 => 'copyright',  0xAA => 'trademark',   0xAB => 'acute',
        0xAC => 'dieresis',   0xAD => 'notequal',    0xAE => 'AE',
        0xAF => 'Oslash',
    ];

    public function getName(): string { return 'MacRomanEncoding'; }

    public function getGlyphName(int $charCode): ?string
    {
        if ($charCode >= 0x20 && $charCode <= 0x7E) {
            return (new WinAnsiEncoding())->getGlyphName($charCode);
        }
        return self::TABLE[$charCode] ?? null;
    }
}
