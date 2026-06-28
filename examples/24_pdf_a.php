<?php

/**
 * Example 24: PDF/A archival conformance (ISO 19005)
 *
 * enablePdfA() adds an sRGB OutputIntent, PDF/A identification metadata in XMP,
 * and a document ID.  For conformance you must also embed every font (use a
 * TTF/OTF path or addUnicodeFont(), not the standard 14 fonts) and avoid
 * disallowed features such as encryption.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Text};
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$doc = PdfDocument::create();
$doc->setTitle('Annual Report 2025')->setAuthor('Papier')->setSubject('Archival copy');

// PDF/A-2b conformance.
$doc->enablePdfA(2, 'B');

// Fonts MUST be embedded for PDF/A — use a font file, not a standard-14 name.
$font = $doc->addFont(__DIR__ . '/Lato-Regular.ttf', '', subset: true);

$page = $doc->addPage();
$page->add(
    Text::write('Annual Report 2025')->at(72, 770)->font($font, 22)->color(Color::hex('#1a1a2e')),
    Text::write('This document is suitable for long-term archiving (PDF/A-2b).')
        ->at(72, 735)->font($font, 12),
);

$file = "$outDir/24_pdf_a.pdf";
$doc->save($file);
echo "Created: 24_pdf_a.pdf (" . number_format(filesize($file)) . " bytes)\n";

// Show the conformance markers.
$pdf = file_get_contents($file);
echo "Has sRGB OutputIntent:    " . (str_contains($pdf, '/GTS_PDFA1') ? 'yes' : 'no') . "\n";
echo "Embedded ICC profile:     " . (str_contains($pdf, '/DestOutputProfile') ? 'yes' : 'no') . "\n";

$parser = new PdfParser($pdf);
$parser->parse();
$xmp = $parser->getXmpMetadata() ?? '';
echo "XMP declares PDF/A part:  " . (preg_match('/<pdfaid:part>(\d)/', $xmp, $m) ? $m[1] : '?') . "\n";
echo "XMP conformance level:    " . (preg_match('/<pdfaid:conformance>(\w)/', $xmp, $m) ? $m[1] : '?') . "\n";

echo "\nNote: run a validator (e.g. veraPDF) for full conformance checking.\n";
echo "Done.\n";
