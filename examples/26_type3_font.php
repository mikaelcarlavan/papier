<?php

/**
 * Example 26: Type 3 (user-defined) fonts
 *
 * A Type 3 font defines each glyph with a content stream of arbitrary PDF
 * graphics operators — useful for icon fonts, custom symbols, or bitmap glyphs.
 * Papier emits the full font dictionary (FontMatrix, CharProcs, Encoding,
 * Widths, Resources) so the glyphs render and can be positioned like any text.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Color, Text};
use Papier\Font\Type3Font;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$doc  = PdfDocument::create();
$doc->setTitle('Type 3 font demo');
$label = $doc->addFont('Helvetica');

// Define a small symbol font. FontMatrix maps the 0..1000 glyph space to text space.
$icons = new Type3Font();
$icons->setFontBBox(0, 0, 1000, 1000);

// 'A' (code 65): a filled disc.
$disc = new ContentStream();
$disc->drawCircle(500, 350, 350)->fill();
$icons->addGlyph(65, $disc, 1000, 'disc');

// 'B' (code 66): a filled square.
$square = new ContentStream();
$square->rectangle(150, 0, 700, 700)->fill();
$icons->addGlyph(66, $square, 1000, 'square');

// 'C' (code 67): a triangle.
$tri = new ContentStream();
$tri->moveTo(500, 700)->lineTo(150, 0)->lineTo(850, 0)->closePath()->fill();
$icons->addGlyph(67, $tri, 1000, 'triangle');

$iconFont = $doc->registerFont($icons, 'Icons');

$page = $doc->addPage();
$page->add(
    Text::write('Type 3 (user-defined glyph) font')->at(72, 770)->font($label, 16)->color(Color::hex('#1a1a2e')),
    // 'ABC' renders as disc, square, triangle.
    Text::write('ABC')->at(72, 680)->font($iconFont, 48)->color(Color::hex('#c0392b')),
    Text::write('(disc, square, triangle — each a Type 3 glyph)')->at(72, 660)->font($label, 10),
);

$file = "$outDir/26_type3_font.pdf";
$doc->save($file);
echo "Created: 26_type3_font.pdf (" . number_format(filesize($file)) . " bytes)\n";

$pdf = file_get_contents($file);
echo "Has /CharProcs: " . (str_contains($pdf, '/CharProcs') ? 'yes' : 'no') . "\n";
echo "Has /Differences encoding: " . (str_contains($pdf, '/Differences') ? 'yes' : 'no') . "\n";

echo "\nDone.\n";
