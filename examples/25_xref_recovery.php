<?php

/**
 * Example 25: Recovering damaged PDFs
 *
 * Real-world files often have a broken or missing cross-reference table. The
 * parser falls back to scanning the byte stream for objects and the catalog, so
 * such files still open. This demo deliberately corrupts a valid PDF and reads
 * it back.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// 1. A normal document.
$doc  = PdfDocument::create();
$doc->setTitle('Recovery demo')->setAuthor('Papier');
$font = $doc->addFont('Helvetica');
foreach ([1, 2, 3] as $n) {
    $page = $doc->addPage();
    $page->add(Text::write("Content on page $n")->at(72, 750)->font($font, 14));
}
$pdf = $doc->toString();

// 2. Corrupt the startxref pointer.
$corruptStartxref = preg_replace('/startxref\s+\d+/', "startxref\n987654321", $pdf, 1);
file_put_contents("$outDir/25_corrupt_startxref.pdf", $corruptStartxref);

// 3. Strip the whole xref + trailer.
$xrefPos   = strrpos($pdf, "\nxref");
$noXref    = substr($pdf, 0, $xrefPos) . "\n%%EOF\n";
file_put_contents("$outDir/25_no_xref.pdf", $noXref);

echo "Created: 25_corrupt_startxref.pdf and 25_no_xref.pdf\n\n";

foreach ([
    'corrupt startxref' => $corruptStartxref,
    'xref removed'      => $noXref,
] as $label => $bytes) {
    $parser = new PdfParser($bytes);
    $parser->parse();
    echo "Recovered ($label):\n";
    echo "  pages: {$parser->getPageCount()}\n";
    echo "  page 2 text: " . trim($parser->extractTextFromPageNumber(2)) . "\n";
    echo "  title: " . ($parser->getTitle() ?: '(unrecoverable without trailer)') . "\n\n";
}

echo "Done.\n";
