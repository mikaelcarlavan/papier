<?php

declare(strict_types=1);

namespace Papier\Font\Encoding;

/**
 * Windows Latin (cp1252 / WinAnsiEncoding) (ISO 32000-1 Annex D.2).
 *
 * This is the most common encoding for Western European text in PDF.
 */
final class WinAnsiEncoding extends Encoding
{
    /** Glyph name table for code points 0x80–0x9F (Windows-1252 extensions). */
    private const WIN_EXT = [
        0x80 => 'Euro',        0x82 => 'quotesinglbase', 0x83 => 'florin',
        0x84 => 'quotedblbase',0x85 => 'ellipsis',       0x86 => 'dagger',
        0x87 => 'daggerdbl',   0x88 => 'circumflex',     0x89 => 'perthousand',
        0x8A => 'Scaron',      0x8B => 'guilsinglleft',  0x8C => 'OE',
        0x8E => 'Zcaron',      0x91 => 'quoteleft',      0x92 => 'quoteright',
        0x93 => 'quotedblleft',0x94 => 'quotedblright',  0x95 => 'bullet',
        0x96 => 'endash',      0x97 => 'emdash',         0x98 => 'tilde',
        0x99 => 'trademark',   0x9A => 'scaron',         0x9B => 'guilsinglright',
        0x9C => 'oe',          0x9E => 'zcaron',         0x9F => 'Ydieresis',
    ];

    /** Standard Latin-1 glyph names for 0xA0–0xFF. */
    private const LATIN1 = [
        0xA0 => 'space',      0xA1 => 'exclamdown', 0xA2 => 'cent',
        0xA3 => 'sterling',   0xA4 => 'currency',   0xA5 => 'yen',
        0xA6 => 'brokenbar',  0xA7 => 'section',    0xA8 => 'dieresis',
        0xA9 => 'copyright',  0xAA => 'ordfeminine',0xAB => 'guillemotleft',
        0xAC => 'logicalnot', 0xAD => 'hyphen',     0xAE => 'registered',
        0xAF => 'macron',     0xB0 => 'degree',     0xB1 => 'plusminus',
        0xB2 => 'twosuperior',0xB3 => 'threesuperior',0xB4 => 'acute',
        0xB5 => 'mu',         0xB6 => 'paragraph',  0xB7 => 'periodcentered',
        0xB8 => 'cedilla',    0xB9 => 'onesuperior',0xBA => 'ordmasculine',
        0xBB => 'guillemotright',0xBC => 'onequarter',0xBD => 'onehalf',
        0xBE => 'threequarters',0xBF => 'questiondown',
        0xC0 => 'Agrave',    0xC1 => 'Aacute',    0xC2 => 'Acircumflex',
        0xC3 => 'Atilde',    0xC4 => 'Adieresis', 0xC5 => 'Aring',
        0xC6 => 'AE',        0xC7 => 'Ccedilla',  0xC8 => 'Egrave',
        0xC9 => 'Eacute',    0xCA => 'Ecircumflex',0xCB => 'Edieresis',
        0xCC => 'Igrave',    0xCD => 'Iacute',    0xCE => 'Icircumflex',
        0xCF => 'Idieresis', 0xD0 => 'Eth',       0xD1 => 'Ntilde',
        0xD2 => 'Ograve',    0xD3 => 'Oacute',    0xD4 => 'Ocircumflex',
        0xD5 => 'Otilde',    0xD6 => 'Odieresis', 0xD7 => 'multiply',
        0xD8 => 'Oslash',    0xD9 => 'Ugrave',    0xDA => 'Uacute',
        0xDB => 'Ucircumflex',0xDC => 'Udieresis',0xDD => 'Yacute',
        0xDE => 'Thorn',     0xDF => 'germandbls',
        0xE0 => 'agrave',    0xE1 => 'aacute',    0xE2 => 'acircumflex',
        0xE3 => 'atilde',    0xE4 => 'adieresis', 0xE5 => 'aring',
        0xE6 => 'ae',        0xE7 => 'ccedilla',  0xE8 => 'egrave',
        0xE9 => 'eacute',    0xEA => 'ecircumflex',0xEB => 'edieresis',
        0xEC => 'igrave',    0xED => 'iacute',    0xEE => 'icircumflex',
        0xEF => 'idieresis', 0xF0 => 'eth',       0xF1 => 'ntilde',
        0xF2 => 'ograve',    0xF3 => 'oacute',    0xF4 => 'ocircumflex',
        0xF5 => 'otilde',    0xF6 => 'odieresis', 0xF7 => 'divide',
        0xF8 => 'oslash',    0xF9 => 'ugrave',    0xFA => 'uacute',
        0xFB => 'ucircumflex',0xFC => 'udieresis',0xFD => 'yacute',
        0xFE => 'thorn',     0xFF => 'ydieresis',
    ];

    private const GLYPH_NAMES = [
        32 => 'space', 33 => 'exclam', 34 => 'quotedbl', 35 => 'numbersign',
        36 => 'dollar', 37 => 'percent', 38 => 'ampersand', 39 => 'quotesingle',
        40 => 'parenleft', 41 => 'parenright', 42 => 'asterisk', 43 => 'plus',
        44 => 'comma', 45 => 'hyphen', 46 => 'period', 47 => 'slash',
        48 => 'zero', 49 => 'one', 50 => 'two', 51 => 'three', 52 => 'four',
        53 => 'five', 54 => 'six', 55 => 'seven', 56 => 'eight', 57 => 'nine',
        58 => 'colon', 59 => 'semicolon', 60 => 'less', 61 => 'equal',
        62 => 'greater', 63 => 'question', 64 => 'at',
        65 => 'A', 66 => 'B', 67 => 'C', 68 => 'D', 69 => 'E', 70 => 'F',
        71 => 'G', 72 => 'H', 73 => 'I', 74 => 'J', 75 => 'K', 76 => 'L',
        77 => 'M', 78 => 'N', 79 => 'O', 80 => 'P', 81 => 'Q', 82 => 'R',
        83 => 'S', 84 => 'T', 85 => 'U', 86 => 'V', 87 => 'W', 88 => 'X',
        89 => 'Y', 90 => 'Z',
        91 => 'bracketleft', 92 => 'backslash', 93 => 'bracketright',
        94 => 'asciicircum', 95 => 'underscore', 96 => 'grave',
        97 => 'a', 98 => 'b', 99 => 'c', 100 => 'd', 101 => 'e', 102 => 'f',
        103 => 'g', 104 => 'h', 105 => 'i', 106 => 'j', 107 => 'k', 108 => 'l',
        109 => 'm', 110 => 'n', 111 => 'o', 112 => 'p', 113 => 'q', 114 => 'r',
        115 => 's', 116 => 't', 117 => 'u', 118 => 'v', 119 => 'w', 120 => 'x',
        121 => 'y', 122 => 'z',
        123 => 'braceleft', 124 => 'bar', 125 => 'braceright', 126 => 'asciitilde',
    ];

    public function getName(): string { return 'WinAnsiEncoding'; }

    public function getGlyphName(int $charCode): ?string
    {
        return self::GLYPH_NAMES[$charCode]
            ?? self::WIN_EXT[$charCode]
            ?? self::LATIN1[$charCode]
            ?? null;
    }
}
