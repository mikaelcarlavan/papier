<?php

/**
 * Example 29: Layout- and encoding-aware text extraction
 *
 * The parser walks the content stream, tracks the text state, and decodes shown
 * strings through each font's embedded /ToUnicode CMap — so accented Latin,
 * subset, and Type 0 (composite/CJK) fonts extract correctly, with spaces and
 * line breaks inferred from positioning.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// Build a document mixing a standard font, a subset TrueType font, and a
// full-Unicode (Type 0) font.
$doc      = PdfDocument::create();
$doc->setTitle('Extraction demo');
$helv     = $doc->addFont('Helvetica');
$lato     = $doc->addFont(__DIR__ . '/Lato-Regular.ttf', '', subset: true);
$unicode  = $doc->addUnicodeFont(__DIR__ . '/Lato-Regular.ttf');

$page = $doc->addPage();
$page->add(
    Text::write('Invoice #2026-0042')->at(72, 760)->font($helv, 16),
    Text::write('Café Lato — déjà vu, naïve, €42.50')->at(72, 730)->font($lato, 13),
    Text::write('Composite font: résumé, Zürich, œuvre')->at(72, 705)->font($unicode, 13),
    Text::write('Second paragraph on a new line.')->at(72, 675)->font($helv, 12),
);

$file = "$outDir/29_text_extraction.pdf";
$doc->save($file);
echo "Created: 29_text_extraction.pdf\n\n";

// Extract it back.
$parser = new PdfParser(file_get_contents($file));
$parser->parse();

echo "--- Extracted text ---\n";
echo $parser->extractText() . "\n";

echo "\n--- Round-trip check ---\n";
$text = $parser->extractText();
foreach (['Invoice #2026-0042', 'Café Lato', 'résumé', 'new line'] as $needle) {
    echo (str_contains($text, $needle) ? '  ✓ ' : '  ✗ ') . $needle . "\n";
}

echo "\nDone.\n";
