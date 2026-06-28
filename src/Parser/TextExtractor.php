<?php

declare(strict_types=1);

namespace Papier\Parser;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfStream, PdfString};

/**
 * Layout-aware text extractor (ISO 32000-1 §9.4, §9.10).
 *
 * Walks a page's content stream tracking the text state (font, text matrix,
 * leading) and decodes shown strings through each font's embedded /ToUnicode
 * CMap (or /Encoding), so Type 0 / CJK / subset fonts extract correctly.
 * Spaces and line breaks are inferred from TJ adjustments and vertical moves.
 */
final class TextExtractor
{
    public function __construct(private readonly PdfParser $parser) {}

    /**
     * Extract text from a page dictionary, preserving rough reading order.
     */
    public function extractPage(PdfDictionary $page): string
    {
        $resources = $this->parser->resolve($page->get('Resources') ?? new \Papier\Objects\PdfNull());
        $resources = $resources instanceof PdfDictionary ? $resources : null;

        $contentRef = $page->get('Contents');
        if ($contentRef === null) {
            return '';
        }
        $data = '';
        $refs = $contentRef instanceof PdfArray ? $contentRef->getItems() : [$contentRef];
        foreach ($refs as $ref) {
            $obj = $this->parser->resolve($ref);
            if ($obj instanceof PdfStream) {
                $data .= $obj->decode() . "\n";
            }
        }

        return $data === '' ? '' : $this->run($data, $this->buildFonts($resources), $resources, 0);
    }

    /**
     * Build a Unicode decoder for every font in a resource dictionary.
     *
     * @return array<string, array{bytes:int, map:array<int,string>, encoding:?string}>
     */
    private function buildFonts(?PdfDictionary $resources): array
    {
        $fonts = [];
        if ($resources === null) {
            return $fonts;
        }
        $fontDict = $this->parser->resolve($resources->get('Font') ?? new \Papier\Objects\PdfNull());
        if (!$fontDict instanceof PdfDictionary) {
            return $fonts;
        }
        foreach ($fontDict->getEntries() as $name => $ref) {
            $font = $this->parser->resolve($ref);
            if ($font instanceof PdfDictionary) {
                $fonts[$name] = $this->buildDecoder($font);
            }
        }
        return $fonts;
    }

    /**
     * @return array{bytes:int, map:array<int,string>, encoding:?string}
     */
    private function buildDecoder(PdfDictionary $font): array
    {
        $subtype = $font->get('Subtype');
        $isType0 = $subtype instanceof PdfName && $subtype->getValue() === 'Type0';

        $decoder = ['bytes' => $isType0 ? 2 : 1, 'map' => [], 'encoding' => null];

        // Prefer the embedded ToUnicode CMap.
        $toUni = $this->parser->resolve($font->get('ToUnicode') ?? new \Papier\Objects\PdfNull());
        if ($toUni instanceof PdfStream) {
            [$bytes, $map] = $this->parseToUnicode($toUni->decode());
            if (!empty($map)) {
                $decoder['map'] = $map;
                if ($bytes > 0) {
                    $decoder['bytes'] = $bytes;
                }
                return $decoder;
            }
        }

        // Simple fonts: fall back to the named encoding.
        if (!$isType0) {
            $enc = $font->get('Encoding');
            $encName = $enc instanceof PdfName ? $enc->getValue() : 'WinAnsiEncoding';
            $decoder['encoding'] = $encName;
        }
        return $decoder;
    }

    /**
     * Parse a ToUnicode CMap's bfchar/bfrange sections.
     *
     * @return array{0:int, 1:array<int,string>} [code byte-width, code → UTF-8]
     */
    private function parseToUnicode(string $cmap): array
    {
        $bytes = 0;
        // codespacerange determines the code width.
        if (preg_match('/begincodespacerange(.*?)endcodespacerange/s', $cmap, $m)
            && preg_match('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $m[1], $cm)) {
            $bytes = (int) (strlen($cm[1]) / 2);
        }

        $map = [];

        // bfchar: <src> <dst>
        if (preg_match_all('/beginbfchar(.*?)endbfchar/s', $cmap, $blocks)) {
            foreach ($blocks[1] as $block) {
                if (preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $block, $pairs, PREG_SET_ORDER)) {
                    foreach ($pairs as $p) {
                        $map[hexdec($p[1])] = $this->utf16beToUtf8($p[2]);
                    }
                }
            }
        }

        // bfrange: <lo> <hi> <dst>  or  <lo> <hi> [<d1> <d2> …]
        if (preg_match_all('/beginbfrange(.*?)endbfrange/s', $cmap, $blocks)) {
            foreach ($blocks[1] as $block) {
                // Array form.
                if (preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*\[(.*?)\]/s', $block, $arr, PREG_SET_ORDER)) {
                    foreach ($arr as $r) {
                        $lo = hexdec($r[1]);
                        if (preg_match_all('/<([0-9A-Fa-f]+)>/', $r[3], $dsts)) {
                            foreach ($dsts[1] as $i => $hex) {
                                $map[$lo + $i] = $this->utf16beToUtf8($hex);
                            }
                        }
                    }
                }
                // Scalar form.
                if (preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $block, $rng, PREG_SET_ORDER)) {
                    foreach ($rng as $r) {
                        $lo  = hexdec($r[1]);
                        $hi  = hexdec($r[2]);
                        $dst = hexdec($r[3]);
                        for ($c = $lo; $c <= $hi && $c - $lo < 65536; $c++) {
                            $map[$c] = $this->codepointToUtf8($dst + ($c - $lo));
                        }
                    }
                }
            }
        }

        return [$bytes, $map];
    }

    /**
     * Walk the content stream operators and assemble text.
     *
     * @param array<string, array{bytes:int, map:array<int,string>, encoding:?string}> $fonts
     */
    private function run(string $content, array $fonts, ?PdfDictionary $resources, int $depth): string
    {
        $tok = new Tokenizer($content);

        $operands = [];
        $out      = '';
        $line     = '';
        $lastY    = null;
        $curFont  = null;
        $fontSize = 0.0;
        // Text matrix translation (e, f) — sufficient for line grouping.
        $tx = 0.0; $ty = 0.0; $lx = 0.0; $ly = 0.0; $leading = 0.0;

        $flushLine = function () use (&$out, &$line) {
            if (trim($line) !== '') {
                $out .= rtrim($line) . "\n";
            }
            $line = '';
        };

        while (true) {
            $t = $tok->nextToken();
            if ($t['type'] === Tokenizer::T_EOF) {
                break;
            }

            switch ($t['type']) {
                case Tokenizer::T_INTEGER:
                case Tokenizer::T_REAL:
                    $operands[] = (float) $t['value'];
                    break;
                case Tokenizer::T_STRING:
                case Tokenizer::T_HEXSTR:
                    $operands[] = ['str' => $t['value']];
                    break;
                case Tokenizer::T_NAME:
                    $operands[] = ['name' => $t['value']];
                    break;
                case Tokenizer::T_ARRAY_OPEN:
                    $operands[] = $this->readArray($tok);
                    break;
                case Tokenizer::T_KEYWORD:
                    $op = $t['value'];
                    switch ($op) {
                        case 'BT':
                            $tx = $ty = $lx = $ly = 0.0;
                            break;
                        case 'Tf':
                            $curFont  = isset($operands[count($operands) - 2]['name']) ? $operands[count($operands) - 2]['name'] : $curFont;
                            $fontSize = (float) end($operands);
                            break;
                        case 'Td':
                        case 'TD':
                            $a = $this->nums($operands, 2);
                            $lx += $a[0]; $ly += $a[1];
                            $tx = $lx; $ty = $ly;
                            if ($op === 'TD') { $leading = -$a[1]; }
                            $this->maybeBreak($ty, $lastY, $fontSize, $flushLine);
                            break;
                        case 'TL':
                            $leading = (float) end($operands);
                            break;
                        case 'Tm':
                            $a = $this->nums($operands, 6);
                            $lx = $tx = $a[4]; $ly = $ty = $a[5];
                            $this->maybeBreak($ty, $lastY, $fontSize, $flushLine);
                            break;
                        case 'T*':
                            $ly -= $leading; $ty = $ly; $lx = $tx = $lx;
                            $this->maybeBreak($ty, $lastY, $fontSize, $flushLine);
                            break;
                        case 'Tj':
                        case "'":
                        case '"':
                            if ($op !== 'Tj') { $ly -= $leading; $ty = $ly; $this->maybeBreak($ty, $lastY, $fontSize, $flushLine); }
                            $s = end($operands);
                            if (is_array($s) && isset($s['str'])) {
                                $line .= $this->decode($fonts, $curFont, $s['str']);
                            }
                            $lastY = $ty;
                            break;
                        case 'TJ':
                            $arr = end($operands);
                            if (is_array($arr) && isset($arr['array'])) {
                                foreach ($arr['array'] as $el) {
                                    if (is_array($el)) {
                                        $line .= $this->decode($fonts, $curFont, $el['str']);
                                    } elseif ($el <= -180) {
                                        // Large negative adjustment ⇒ inter-word gap.
                                        $line .= ' ';
                                    }
                                }
                            }
                            $lastY = $ty;
                            break;
                        case 'Do':
                            // Recurse into a Form XObject so imported/merged/N-up
                            // content (drawn via Do) is also extracted.
                            $nameOp = end($operands);
                            if ($depth < 8 && is_array($nameOp) && isset($nameOp['name'])) {
                                $sub = $this->extractXObject($nameOp['name'], $resources, $depth);
                                if ($sub !== '') {
                                    $flushLine();
                                    $out .= $sub . "\n";
                                }
                            }
                            break;
                    }
                    $operands = [];
                    break;
                default:
                    // ignore other delimiters
                    break;
            }
        }
        $flushLine();
        return rtrim($out, "\n");
    }

    /** Insert a line break when the text position drops to a new line. */
    private function maybeBreak(float $y, ?float &$lastY, float $fontSize, callable $flushLine): void
    {
        if ($lastY !== null && abs($y - $lastY) > max(2.0, $fontSize * 0.5)) {
            $flushLine();
        }
    }

    /** Recurse into a Form XObject referenced by name and extract its text. */
    private function extractXObject(string $name, ?PdfDictionary $resources, int $depth): string
    {
        if ($resources === null) {
            return '';
        }
        $xobjects = $this->parser->resolve($resources->get('XObject') ?? new \Papier\Objects\PdfNull());
        if (!$xobjects instanceof PdfDictionary) {
            return '';
        }
        $xobj = $this->parser->resolve($xobjects->get($name) ?? new \Papier\Objects\PdfNull());
        if (!$xobj instanceof PdfStream) {
            return '';
        }
        $sub = $xobj->getDictionary()->get('Subtype');
        if (!$sub instanceof PdfName || $sub->getValue() !== 'Form') {
            return '';
        }
        $xres = $this->parser->resolve($xobj->getDictionary()->get('Resources') ?? new \Papier\Objects\PdfNull());
        $xres = $xres instanceof PdfDictionary ? $xres : $resources;
        return $this->run($xobj->decode(), $this->buildFonts($xres), $xres, $depth + 1);
    }

    /**
     * Decode a shown string through the active font's decoder.
     *
     * @param array<string, array{bytes:int, map:array<int,string>, encoding:?string}> $fonts
     */
    private function decode(array $fonts, ?string $fontName, string $bytes): string
    {
        $dec = $fontName !== null ? ($fonts[$fontName] ?? null) : null;
        if ($dec === null) {
            return $this->winAnsiBytes($bytes);
        }

        $out = '';
        $w   = $dec['bytes'];
        $len = strlen($bytes);
        for ($i = 0; $i + $w <= $len; $i += $w) {
            $code = 0;
            for ($j = 0; $j < $w; $j++) {
                $code = ($code << 8) | ord($bytes[$i + $j]);
            }
            if (isset($dec['map'][$code])) {
                $out .= $dec['map'][$code];
            } elseif ($w === 1) {
                $out .= $this->winAnsiByte($code);
            }
            // Unknown composite code: skip silently.
        }
        return $out;
    }

    /** @return array{array:array<int, float|array{str:string}>} */
    private function readArray(Tokenizer $tok): array
    {
        $items = [];
        while (true) {
            $t = $tok->nextToken();
            if ($t['type'] === Tokenizer::T_ARRAY_CLOSE || $t['type'] === Tokenizer::T_EOF) {
                break;
            }
            if ($t['type'] === Tokenizer::T_STRING || $t['type'] === Tokenizer::T_HEXSTR) {
                $items[] = ['str' => $t['value']];
            } elseif ($t['type'] === Tokenizer::T_INTEGER || $t['type'] === Tokenizer::T_REAL) {
                $items[] = (float) $t['value'];
            }
        }
        return ['array' => $items];
    }

    /** @param array<int, mixed> $operands @return float[] */
    private function nums(array $operands, int $count): array
    {
        $nums = array_values(array_filter($operands, 'is_float'));
        return array_slice(array_pad($nums, -$count, 0.0), -$count);
    }

    private function winAnsiBytes(string $s): string
    {
        $out = '';
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $out .= $this->winAnsiByte(ord($s[$i]));
        }
        return $out;
    }

    private function winAnsiByte(int $code): string
    {
        // Latin-1 / Windows-1252 byte → UTF-8.
        return (string) @mb_convert_encoding(chr($code), 'UTF-8', 'Windows-1252');
    }

    private function utf16beToUtf8(string $hex): string
    {
        $bin = hex2bin(strlen($hex) % 2 ? '0' . $hex : $hex);
        if ($bin === false) {
            return '';
        }
        return (string) @mb_convert_encoding($bin, 'UTF-8', 'UTF-16BE');
    }

    private function codepointToUtf8(int $cp): string
    {
        return mb_chr($cp, 'UTF-8') ?: '';
    }
}
