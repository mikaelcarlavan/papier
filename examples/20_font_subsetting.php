<?php

/**
 * Example 20: TrueType font subsetting + ToUnicode
 *
 * Embedding the full font program is wasteful when only a handful of glyphs are
 * used. addFont(..., subset: true) strips unused glyph outlines, keeping the
 * file small, and a /ToUnicode CMap is generated automatically so the text
 * remains searchable and copy-pasteable.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Text};
use Papier\Parser\PdfParser;

$fontPath = __DIR__ . '/Lato-Regular.ttf';
$outDir   = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

function buildWith(string $fontPath, bool $subset): string
{
    $doc  = PdfDocument::create();
    $doc->setTitle('Font subsetting demo');
    $lato = $doc->addFont($fontPath, '', subset: $subset);
    $page = $doc->addPage();
    $page->add(
        Text::write('Subsetting & ToUnicode')->at(72, 760)->font($lato, 24)->color(Color::hex('#1a1a2e')),
        Text::write('The quick brown fox jumps over the lazy dog. 0123456789')
            ->at(72, 720)->font($lato, 13),
        Text::write('Accented Latin: café, naïve, résumé, Zürich.')
            ->at(72, 695)->font($lato, 13),
    );
    return $doc->toString();
}

if (!file_exists($fontPath)) {
    echo "Font not found: $fontPath\n";
    exit(1);
}

$full   = buildWith($fontPath, false);
$subset = buildWith($fontPath, true);

file_put_contents("$outDir/20_full.pdf", $full);
file_put_contents("$outDir/20_subset.pdf", $subset);

echo "Created: 20_full.pdf    (" . number_format(strlen($full)) . " bytes, full font embedded)\n";
echo "Created: 20_subset.pdf  (" . number_format(strlen($subset)) . " bytes, subset embedded)\n";
printf("Size reduction: %.1f%%\n\n", 100 * (1 - strlen($subset) / strlen($full)));

// Subset tag + ToUnicode are present, and the file still parses.
if (preg_match('/\/BaseFont\s*\/([A-Z]{6})\+/', $subset, $m)) {
    echo "Subset tag on /BaseFont: {$m[1]}+\n";
}
echo "ToUnicode CMap present:  " . (str_contains($subset, '/ToUnicode') ? 'yes' : 'no') . "\n";

$parser = new PdfParser($subset);
$parser->parse();
echo "Re-parsed subset PDF — pages: {$parser->getPageCount()}, fonts: "
   . count($parser->getFonts()) . "\n";

echo "\nDone.\n";
