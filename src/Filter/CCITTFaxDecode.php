<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\{PdfBoolean, PdfDictionary, PdfInteger, PdfObject};

/**
 * CCITTFaxDecode filter (ISO 32000-1 §7.4.6) — Group 4 (T.6, K < 0).
 *
 * Implements two-dimensional ("pure 2-D") Group 4 fax coding, by far the most
 * common form in PDF (`K = -1`).  Group 3 (`K >= 0`) is not decoded here; such
 * data is returned unchanged for the viewer to render.
 *
 * Parameters (DecodeParms): Columns (1728), Rows, K, BlackIs1 (false),
 * EncodedByteAlign (false).
 */
final class CCITTFaxDecode implements FilterInterface
{
    // 2-D mode codes (bit strings).
    private const MODE_PASS = '0001';
    private const MODE_HORIZ = '001';
    private const MODE_V0 = '1';
    private const MODE_VR1 = '011';
    private const MODE_VR2 = '000011';
    private const MODE_VR3 = '0000011';
    private const MODE_VL1 = '010';
    private const MODE_VL2 = '000010';
    private const MODE_VL3 = '0000010';

    /** @var array<int,string>|null run length → white code */
    private static ?array $whiteCodes = null;
    /** @var array<int,string>|null run length → black code */
    private static ?array $blackCodes = null;
    /** @var array<string,int>|null white code → run length */
    private static ?array $whiteDecode = null;
    /** @var array<string,int>|null black code → run length */
    private static ?array $blackDecode = null;

    public function decode(string $data, ?PdfObject $params = null): string
    {
        [$columns, $rows, $k, $blackIs1, $byteAlign] = $this->params($params);
        if ($k >= 0) {
            return $data; // Group 3 not supported; pass through.
        }

        self::initTables();
        $reader = new class($data) {
            private int $bit = 0;
            public function __construct(private readonly string $d) {}
            public function read1(): ?int
            {
                $byte = $this->bit >> 3;
                if ($byte >= strlen($this->d)) { return null; }
                $b = (ord($this->d[$byte]) >> (7 - ($this->bit & 7))) & 1;
                $this->bit++;
                return $b;
            }
            public function align(): void { $this->bit = ($this->bit + 7) & ~7; }
            public function eof(): bool { return ($this->bit >> 3) >= strlen($this->d); }
        };

        // Reference line: changing-element positions; an imaginary all-white line.
        $ref = [$columns, $columns];
        $outRows = [];
        $rowCount = 0;

        while (!$reader->eof()) {
            if ($rows > 0 && $rowCount >= $rows) {
                break;
            }
            if ($byteAlign && $rowCount > 0) {
                $reader->align();
            }

            $cur   = [];
            $a0    = -1;
            $color = 0; // 0 = white, 1 = black

            while ($a0 < $columns) {
                $mode = $this->readMode($reader);
                if ($mode === null) {
                    break 2; // EOFB or end of data
                }
                [$b1, $b2] = $this->findB1B2($ref, $a0, $color, $columns);

                if ($mode === 'P') {
                    $a0 = $b2;
                } elseif ($mode === 'H') {
                    $start = $a0 < 0 ? 0 : $a0;
                    $run1  = $this->readRun($reader, $color);
                    $run2  = $this->readRun($reader, $color ^ 1);
                    if ($run1 === null || $run2 === null) { break 2; }
                    $a1 = min($start + $run1, $columns);
                    $a2 = min($a1 + $run2, $columns);
                    $cur[] = $a1;
                    $cur[] = $a2;
                    $a0 = $a2;
                } else { // vertical mode: $mode is an int offset from b1
                    $a1 = max(0, min($b1 + $mode, $columns));
                    $cur[] = $a1;
                    $a0 = $a1;
                    $color ^= 1;
                }
            }

            $outRows[] = $this->rowToBits($cur, $columns, $blackIs1);
            $ref = $cur;
            $ref[] = $columns;
            $ref[] = $columns;
            $rowCount++;
        }

        return implode('', $outRows);
    }

    public function encode(string $data, ?PdfObject $params = null): string
    {
        [$columns, $rows, $k, $blackIs1, $byteAlign] = $this->params($params);
        self::initTables();

        $bytesPerRow = intdiv($columns + 7, 8);
        $rowCount    = $rows > 0 ? $rows : intdiv(strlen($data), $bytesPerRow);

        $bits = '';
        $ref  = $this->bitsToChanges(str_repeat("\x00", $bytesPerRow), $columns, $blackIs1, true);

        for ($r = 0; $r < $rowCount; $r++) {
            $row = substr($data, $r * $bytesPerRow, $bytesPerRow);
            $cur = $this->bitsToChanges($row, $columns, $blackIs1, false);

            $bits .= $this->encodeRow($cur, $ref, $columns);
            if ($byteAlign) {
                $bits = str_pad($bits, (int) (ceil(strlen($bits) / 8) * 8), '0');
            }
            $ref = $cur;
        }

        // End-of-facsimile block (two EOL+mode codes), then byte-pad.
        $bits .= '000000000001' . '000000000001';
        $bits  = str_pad($bits, (int) (ceil(strlen($bits) / 8) * 8), '0');

        return $this->packBits($bits);
    }

    // ── decoding helpers ─────────────────────────────────────────────────────────

    /** Read a 2-D mode; returns 'P', 'H', an int vertical offset, or null at EOFB. */
    private function readMode(object $reader): string|int|null
    {
        // Exact-string lookup (avoids PHP's numeric-string == coercion, where
        // e.g. '001' == '1' would be true).
        static $modes = [
            self::MODE_V0 => 0, self::MODE_VR1 => 1, self::MODE_VL1 => -1,
            self::MODE_HORIZ => 'H', self::MODE_PASS => 'P',
            self::MODE_VR2 => 2, self::MODE_VL2 => -2,
            self::MODE_VR3 => 3, self::MODE_VL3 => -3,
        ];
        $code = '';
        for ($i = 0; $i < 14; $i++) {
            $b = $reader->read1();
            if ($b === null) { return null; }
            $code .= $b;
            if (array_key_exists($code, $modes)) {
                return $modes[$code];
            }
            if ($code === '000000000001') { return null; } // EOFB
        }
        return null;
    }

    /** Read a complete run length (terminating + any makeup codes). */
    private function readRun(object $reader, int $color): ?int
    {
        $decode = $color === 0 ? self::$whiteDecode : self::$blackDecode;
        $total  = 0;
        while (true) {
            $code = '';
            $matched = null;
            for ($i = 0; $i < 14; $i++) {
                $b = $reader->read1();
                if ($b === null) { return null; }
                $code .= $b;
                if (isset($decode[$code])) {
                    $matched = $decode[$code];
                    break;
                }
            }
            if ($matched === null) { return null; }
            $total += $matched;
            if ($matched < 64) {
                return $total; // terminating code ends the run
            }
        }
    }

    /** @return array{0:int,1:int} */
    private function findB1B2(array $ref, int $a0, int $color, int $columns): array
    {
        $count = count($ref);
        for ($i = 0; $i < $count; $i++) {
            if ($ref[$i] > $a0 && ($i % 2) === $color) {
                return [$ref[$i], $ref[$i + 1] ?? $columns];
            }
        }
        return [$columns, $columns];
    }

    private function rowToBits(array $changes, int $columns, bool $blackIs1): string
    {
        $white = $blackIs1 ? 0 : 1;
        $black = $blackIs1 ? 1 : 0;
        $bitsArr = array_fill(0, $columns, $white);
        $pos = 0; $color = 0;
        foreach ($changes as $c) {
            $c = min($c, $columns);
            if ($color === 1) {
                for ($x = $pos; $x < $c; $x++) { $bitsArr[$x] = $black; }
            }
            $pos = $c;
            $color ^= 1;
        }
        $out = '';
        for ($i = 0; $i < $columns; $i += 8) {
            $byte = 0;
            for ($j = 0; $j < 8; $j++) {
                $byte = ($byte << 1) | ($bitsArr[$i + $j] ?? 0);
            }
            $out .= chr($byte);
        }
        return $out;
    }

    // ── encoding helpers ─────────────────────────────────────────────────────────

    /** Convert a packed 1bpp row to its changing-element positions. */
    private function bitsToChanges(string $row, int $columns, bool $blackIs1, bool $forceWhite): array
    {
        $changes = [];
        $prev = 0; // white
        for ($x = 0; $x < $columns; $x++) {
            $bit = (ord($row[$x >> 3] ?? "\x00") >> (7 - ($x & 7))) & 1;
            $isBlack = $forceWhite ? 0 : ($blackIs1 ? $bit : ($bit ^ 1));
            if ($isBlack !== $prev) {
                $changes[] = $x;
                $prev = $isBlack;
            }
        }
        $changes[] = $columns;
        $changes[] = $columns;
        return $changes;
    }

    private function encodeRow(array $cur, array $ref, int $columns): string
    {
        $bits  = '';
        $a0    = -1;
        $color = 0;

        while ($a0 < $columns) {
            [$b1, $b2] = $this->findB1B2($ref, $a0, $color, $columns);
            $a1 = $columns;
            foreach ($cur as $i => $c) {
                if ($c > $a0 && ($i % 2) === $color) { $a1 = $c; break; }
            }

            if ($b2 < $a1) {
                $bits .= self::MODE_PASS;
                $a0 = $b2;
                continue;
            }

            $delta = $a1 - $b1;
            if ($delta >= -3 && $delta <= 3) {
                $bits .= match ((int) $delta) {
                    1  => self::MODE_VR1,  2 => self::MODE_VR2,  3 => self::MODE_VR3,
                    -1 => self::MODE_VL1, -2 => self::MODE_VL2, -3 => self::MODE_VL3,
                    default => self::MODE_V0, // delta == 0
                };
                $a0 = $a1;
                $color ^= 1;
            } else {
                $start = $a0 < 0 ? 0 : $a0;
                $a2 = $columns;
                foreach ($cur as $i => $c) {
                    if ($c > $a1 && ($i % 2) === ($color ^ 1)) { $a2 = $c; break; }
                }
                $run1 = $a1 - $start;
                $run2 = $a2 - $a1;
                $bits .= self::MODE_HORIZ . $this->runCode($run1, $color) . $this->runCode($run2, $color ^ 1);
                $a0 = $a2;
            }
        }
        return $bits;
    }

    private function runCode(int $run, int $color): string
    {
        $codes = $color === 0 ? self::$whiteCodes : self::$blackCodes;
        $out   = '';
        while ($run >= 64) {
            // Largest available makeup ≤ run (makeup codes are multiples of 64).
            $m = (int) (intdiv($run, 64) * 64);
            while ($m > 0 && !isset($codes[$m])) { $m -= 64; }
            if ($m <= 0) { break; }
            $out .= $codes[$m];
            $run -= $m;
        }
        $out .= $codes[$run];
        return $out;
    }

    private function packBits(string $bits): string
    {
        $out = '';
        for ($i = 0, $n = strlen($bits); $i < $n; $i += 8) {
            $out .= chr((int) bindec(substr($bits, $i, 8)));
        }
        return $out;
    }

    // ── parameters ────────────────────────────────────────────────────────────────

    /** @return array{0:int,1:int,2:int,3:bool,4:bool} */
    private function params(?PdfObject $params): array
    {
        $columns = 1728; $rows = 0; $k = 0; $blackIs1 = false; $byteAlign = false;
        if ($params instanceof PdfDictionary) {
            $columns   = $this->intParam($params, 'Columns', 1728);
            $rows      = $this->intParam($params, 'Rows', 0);
            $k         = $this->intParam($params, 'K', 0);
            $blackIs1  = $this->boolParam($params, 'BlackIs1', false);
            $byteAlign = $this->boolParam($params, 'EncodedByteAlign', false);
        }
        return [$columns, $rows, $k, $blackIs1, $byteAlign];
    }

    private function intParam(PdfDictionary $d, string $k, int $default): int
    {
        $v = $d->get($k);
        return $v instanceof PdfInteger ? $v->getValue() : $default;
    }

    private function boolParam(PdfDictionary $d, string $k, bool $default): bool
    {
        $v = $d->get($k);
        return $v instanceof PdfBoolean ? $v->getValue() : $default;
    }

    // ── code tables (ITU-T T.4) ─────────────────────────────────────────────────────

    private static function initTables(): void
    {
        if (self::$whiteCodes !== null) {
            return;
        }
        self::$whiteCodes  = self::whiteTable();
        self::$blackCodes  = self::blackTable();
        self::$whiteDecode = array_flip(self::$whiteCodes);
        self::$blackDecode = array_flip(self::$blackCodes);
    }

    /** @return array<int,string> */
    private static function whiteTable(): array
    {
        $t = [
            0=>'00110101',1=>'000111',2=>'0111',3=>'1000',4=>'1011',5=>'1100',6=>'1110',7=>'1111',
            8=>'10011',9=>'10100',10=>'00111',11=>'01000',12=>'001000',13=>'000011',14=>'110100',15=>'110101',
            16=>'101010',17=>'101011',18=>'0100111',19=>'0001100',20=>'0001000',21=>'0010111',22=>'0000011',23=>'0000100',
            24=>'0101000',25=>'0101011',26=>'0010011',27=>'0100100',28=>'0011000',29=>'00000010',30=>'00000011',31=>'00011010',
            32=>'00011011',33=>'00010010',34=>'00010011',35=>'00010100',36=>'00010101',37=>'00010110',38=>'00010111',39=>'00101000',
            40=>'00101001',41=>'00101010',42=>'00101011',43=>'00101100',44=>'00101101',45=>'00000100',46=>'00000101',47=>'00001010',
            48=>'00001011',49=>'01010010',50=>'01010011',51=>'01010100',52=>'01010101',53=>'00100100',54=>'00100101',55=>'01011000',
            56=>'01011001',57=>'01011010',58=>'01011011',59=>'01001010',60=>'01001011',61=>'00110010',62=>'00110011',63=>'00110100',
            64=>'11011',128=>'10010',192=>'010111',256=>'0110111',320=>'00110110',384=>'00110111',448=>'01100100',512=>'01100101',
            576=>'01101000',640=>'01100111',704=>'011001100',768=>'011001101',832=>'011010010',896=>'011010011',960=>'011010100',
            1024=>'011010101',1088=>'011010110',1152=>'011010111',1216=>'011011000',1280=>'011011001',1344=>'011011010',
            1408=>'011011011',1472=>'010011000',1536=>'010011001',1600=>'010011010',1664=>'011000',1728=>'010011011',
        ];
        return $t + self::sharedMakeup();
    }

    /** @return array<int,string> */
    private static function blackTable(): array
    {
        $t = [
            0=>'0000110111',1=>'010',2=>'11',3=>'10',4=>'011',5=>'0011',6=>'0010',7=>'00011',
            8=>'000101',9=>'000100',10=>'0000100',11=>'0000101',12=>'0000111',13=>'00000100',14=>'00000111',15=>'000011000',
            16=>'0000010111',17=>'0000011000',18=>'0000001000',19=>'00001100111',20=>'00001101000',21=>'00001101100',22=>'00000110111',23=>'00000101000',
            24=>'00000010111',25=>'00000011000',26=>'000011001010',27=>'000011001011',28=>'000011001100',29=>'000011001101',30=>'000001101000',31=>'000001101001',
            32=>'000001101010',33=>'000001101011',34=>'000011010010',35=>'000011010011',36=>'000011010100',37=>'000011010101',38=>'000011010110',39=>'000011010111',
            40=>'000001101100',41=>'000001101101',42=>'000011011010',43=>'000011011011',44=>'000001010100',45=>'000001010101',46=>'000001010110',47=>'000001010111',
            48=>'000001100100',49=>'000001100101',50=>'000001010010',51=>'000001010011',52=>'000000100100',53=>'000000110111',54=>'000000111000',55=>'000000100111',
            56=>'000000101000',57=>'000001011000',58=>'000001011001',59=>'000000101011',60=>'000000101100',61=>'000001011010',62=>'000001100110',63=>'000001100111',
            64=>'0000001111',128=>'000011001000',192=>'000011001001',256=>'000001011011',320=>'000000110011',384=>'000000110100',448=>'000000110101',
            512=>'0000001101100',576=>'0000001101101',640=>'0000001001010',704=>'0000001001011',768=>'0000001001100',832=>'0000001001101',
            896=>'0000001110010',960=>'0000001110011',1024=>'0000001110100',1088=>'0000001110101',1152=>'0000001110110',1216=>'0000001110111',
            1280=>'0000001010010',1344=>'0000001010011',1408=>'0000001010100',1472=>'0000001010101',1536=>'0000001011010',1600=>'0000001011011',
            1664=>'0000001100100',1728=>'0000001100101',
        ];
        return $t + self::sharedMakeup();
    }

    /** Extended makeup codes shared by both colours (1792–2560). @return array<int,string> */
    private static function sharedMakeup(): array
    {
        return [
            1792=>'00000001000',1856=>'00000001100',1920=>'00000001101',1984=>'000000010010',2048=>'000000010011',
            2112=>'000000010100',2176=>'000000010101',2240=>'000000010110',2304=>'000000010111',2368=>'000000011100',
            2432=>'000000011101',2496=>'000000011110',2560=>'000000011111',
        ];
    }
}
