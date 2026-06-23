<?php

/**
 * Example 16: Right-aligned table cells with UTF-8 text (GitHub issue #2)
 *
 * Reproduces the reported problem: in a right-aligned column, values containing
 * the "€" glyph, a non-breaking space (as inserted by NumberFormatter) or a
 * minus sign were pushed left — they gained extra right-hand padding — because
 * cell width was estimated from the raw UTF-8 byte length (a 3-byte "€" counted
 * as three glyphs) using a flat per-character average, while the text is in fact
 * rendered after a UTF-8 → Windows-1252 conversion.
 *
 * The fix makes width measurement use the embedded font's real glyph advance
 * widths, applied to the exact Windows-1252 bytes that are drawn. This example
 * renders the offending layout and prints a numeric before/after comparison so
 * the correction is visible without opening the PDF.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Table, TableCell, Text};
use Papier\Font\TrueTypeFont;

$fontPath = __DIR__ . '/Lato-Regular.ttf';

// 1 mm in points (the issue's helper pt()): 1 mm = 72 / 25.4 pt.
$mm = static fn (float $v): float => $v * 72.0 / 25.4;

// Format currency the way the issue does (de_DE → "1.234,56 €" with a NBSP).
// Fall back to a hand-built equivalent if the intl extension is unavailable.
if (class_exists(\NumberFormatter::class)) {
    $fmt = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);
    $euro = static fn (float $v): string => $fmt->formatCurrency($v, 'EUR');
} else {
    $euro = static function (float $v): string {
        $s = number_format(abs($v), 2, ',', '.');
        return ($v < 0 ? '-' : '') . $s . "\u{00A0}€"; // NBSP + euro sign
    };
}

// ─────────────────────────────────────────────────────────────────────────────
// Build the PDF
// ─────────────────────────────────────────────────────────────────────────────
$doc  = PdfDocument::create();
$doc->setTitle('Issue #2 — right-aligned UTF-8 cells');

$regular = $doc->addFont($fontPath);
$page    = $doc->addPage();

$page->add(
    Text::write('Issue #2 — right-aligned UTF-8 table cells')
        ->at($mm(25), 800)->font($regular, 13)->color(Color::rgb(0.12, 0.22, 0.42)),
    Text::write('Every value in the three numeric columns must share the same right edge (~1 mm padding).')
        ->at($mm(25), 786)->font($regular, 9)->color(Color::gray(0.35)),
);

// 4-column layout mirroring the report: label + three right-aligned amounts.
$table = Table::create($mm(25), 770)
    ->setColumnWidths($mm(80), $mm(30), $mm(30), $mm(30))
    ->setFont($regular, 9, 'Lato-Regular')
    ->setHeaderRows(1)
    ->setHeaderBg(Color::rgb(0.12, 0.22, 0.42))
    ->setHeaderTextColor(Color::white())
    ->setBorder(Color::gray(0.4), 0.5)
    ->setInnerBorder(Color::gray(0.8), 0.3)
    ->setCellPadding(1)
    ->setTextAlign('right');

$table->addRow([
    TableCell::make('Position')->align('left'),
    'Net', 'VAT', 'Gross',
]);

$rows = [
    ['Consulting (UTF-8: café, naïve)', 1234.56,  234.57, 1469.13],
    ['Discount',                        -89.90,   -17.08,  -106.98],
    ['Hosting €/month',                  12.00,     2.28,    14.28],
    ['Large amount',                     98765.40, 18765.43, 117530.83],
];
foreach ($rows as $r) {
    $table->addRow([
        TableCell::make($r[0])->align('left'),
        $euro($r[1]),
        $euro($r[2]),
        $euro($r[3]),
    ]);
}

// Bold total row (the report noted bold also drifted).
$total = array_sum(array_column($rows, 3));
$table->addRow([
    TableCell::make('Total')->align('left'),
    '', '',
    TableCell::make($euro($total)),
]);

$page->add($table);

$doc->save(__DIR__ . '/output/16_table_utf8_alignment.pdf');
echo "Created: 16_table_utf8_alignment.pdf\n";

// ─────────────────────────────────────────────────────────────────────────────
// Numeric sanity check — demonstrate the measurement fix
// ─────────────────────────────────────────────────────────────────────────────
$font   = TrueTypeFont::fromFile($fontPath);
$size   = 9.0;
$sample = $euro(1234.56); // e.g. "1.234,56 €"

// Correct measurement: real glyph advances over the rendered (Windows-1252) bytes.
$correct = $font->stringWidth($sample, $size);

// Old behaviour: flat 0.55 em per raw UTF-8 byte (multi-byte "€"/NBSP over-counted).
$oldEstimate = strlen($sample) * $size * 0.55;

printf("\nMeasurement of %s at %.0f pt:\n", json_encode($sample), $size);
printf("  old strlen×0.55 heuristic : %6.2f pt  (UTF-8 bytes = %d)\n", $oldEstimate, strlen($sample));
printf("  new real-metric width     : %6.2f pt  (rendered glyphs = %d)\n",
    $correct, strlen(\Papier\Font\Encoding\WinAnsiEncoding::fromUtf8($sample)));
printf("  over-estimate removed     : %6.2f pt  → right-edge padding error eliminated\n",
    $oldEstimate - $correct);
