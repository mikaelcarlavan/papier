<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\Filter\CCITTFaxDecode;
use Papier\Objects\{PdfBoolean, PdfDictionary, PdfInteger};

final class CcittFaxTest extends TestCase
{
    /** Build a packed 1bpp image (bit 1 = white) from a grid of 0/1 rows. */
    private function pack(array $grid, int $columns): string
    {
        $out = '';
        foreach ($grid as $row) {
            for ($i = 0; $i < $columns; $i += 8) {
                $byte = 0;
                for ($j = 0; $j < 8; $j++) {
                    $byte = ($byte << 1) | ($row[$i + $j] ?? 1);
                }
                $out .= chr($byte);
            }
        }
        return $out;
    }

    private function params(int $columns, int $rows, bool $blackIs1 = false): PdfDictionary
    {
        $d = new PdfDictionary();
        $d->set('K', new PdfInteger(-1));
        $d->set('Columns', new PdfInteger($columns));
        $d->set('Rows', new PdfInteger($rows));
        if ($blackIs1) { $d->set('BlackIs1', new PdfBoolean(true)); }
        return $d;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('patterns')]
    public function testRoundTrip(array $grid, int $columns): void
    {
        $rows   = count($grid);
        $raw    = $this->pack($grid, $columns);
        $codec  = new CCITTFaxDecode();
        $params = $this->params($columns, $rows);

        $encoded = $codec->encode($raw, $params);
        $decoded = $codec->decode($encoded, $params);

        $this->assertSame(bin2hex($raw), bin2hex($decoded));
    }

    public static function patterns(): array
    {
        $w16 = 16;
        $allWhite = array_fill(0, 3, array_fill(0, $w16, 1));
        $allBlack = array_fill(0, 3, array_fill(0, $w16, 0));

        $stripes = [];
        for ($r = 0; $r < 4; $r++) {
            $row = [];
            for ($c = 0; $c < $w16; $c++) { $row[] = $c % 2; }
            $stripes[] = $row;
        }

        // Wide runs (>64) needing makeup codes: 200 columns, 100 white + 100 black.
        $wide = [];
        for ($r = 0; $r < 3; $r++) {
            $row = array_merge(array_fill(0, 100, 1), array_fill(0, 100, 0));
            $wide[] = $row;
        }

        // Deterministic pseudo-random pattern.
        $rand = [];
        $seed = 12345;
        for ($r = 0; $r < 6; $r++) {
            $row = [];
            for ($c = 0; $c < 24; $c++) {
                $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;
                $row[] = ($seed >> 16) & 1;
            }
            $rand[] = $row;
        }

        return [
            'all white'      => [$allWhite, $w16],
            'all black'      => [$allBlack, $w16],
            'stripes'        => [$stripes, $w16],
            'wide runs'      => [$wide, 200],
            'pseudo-random'  => [$rand, 24],
        ];
    }

    public function testBlackIs1RoundTrip(): void
    {
        $grid    = [[1,1,0,0,1,0,1,0], [0,0,0,0,1,1,1,1]];
        $raw     = $this->pack($grid, 8);
        $codec   = new CCITTFaxDecode();
        $params  = $this->params(8, 2, blackIs1: true);
        $decoded = $codec->decode($codec->encode($raw, $params), $params);
        $this->assertSame(bin2hex($raw), bin2hex($decoded));
    }

    public function testCompressesLargeMostlyWhiteImage(): void
    {
        $columns = 1728;
        $rows    = 50;
        $grid    = array_fill(0, $rows, array_fill(0, $columns, 1)); // all white
        $raw     = $this->pack($grid, $columns);

        $codec   = new CCITTFaxDecode();
        $encoded = $codec->encode($raw, $this->params($columns, $rows));

        // A blank page should compress dramatically (each row ≈ one V0 bit).
        $this->assertLessThan(strlen($raw) / 50, strlen($encoded));
        $this->assertSame(bin2hex($raw), bin2hex($codec->decode($encoded, $this->params($columns, $rows))));
    }

    public function testGroup3PassesThrough(): void
    {
        $codec = new CCITTFaxDecode();
        $d = new PdfDictionary();
        $d->set('K', new PdfInteger(0)); // Group 3 (unsupported) → unchanged
        $this->assertSame('rawdata', $codec->decode('rawdata', $d));
    }
}
