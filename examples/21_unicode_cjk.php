<?php

/**
 * Example 21: Full-Unicode text with Type 0 (composite) fonts
 *
 * addFont() produces a single-byte WinAnsi font limited to 256 codes, which
 * cannot represent CJK, Cyrillic, Greek, etc.  addUnicodeFont() embeds the font
 * as a CIDFontType2 with Identity-H encoding (two-byte glyph codes), giving
 * access to every glyph in the file, with an automatic /ToUnicode CMap and
 * glyph subsetting.
 *
 * Note: the bundled Lato font only contains Latin glyphs, so this demo uses
 * extended-Latin text.  Point addUnicodeFont() at a CJK font (e.g. Noto Sans CJK)
 * to render Chinese/Japanese/Korean.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Text};
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$doc  = PdfDocument::create();
$doc->setTitle('Unicode text demo');

// Composite, full-Unicode font (subset by default).
$uni = $doc->addUnicodeFont(__DIR__ . '/Lato-Regular.ttf');

$page = $doc->addPage();
$page->add(
    Text::write('Full-Unicode (Type 0) text')->at(72, 770)->font($uni, 22)->color(Color::hex('#1a1a2e')),
    Text::write('Extended Latin: café — naïve — Zürich — œuvre — £ € ¥')->at(72, 735)->font($uni, 14),
    Text::write('Greek glyphs render when present in the font file.')->at(72, 710)->font($uni, 12),
    Text::write('Swap in a CJK font to render 日本語 / 中文 / 한국어.')->at(72, 688)->font($uni, 12),
);

$file = "$outDir/21_unicode_cjk.pdf";
$doc->save($file);
echo "Created: 21_unicode_cjk.pdf (" . number_format(filesize($file)) . " bytes)\n";

// Inspect the embedded composite font.
$parser = new PdfParser(file_get_contents($file));
$parser->parse();
$font = $parser->getFonts()[0] ?? null;
echo "Embedded font subtype: " . ($font['subtype'] ?? '?') . "\n";
echo "Pages: {$parser->getPageCount()}\n";

echo "\nDone.\n";
