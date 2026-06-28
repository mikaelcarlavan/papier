<?php

/**
 * Example 33: Page operations & repeating elements
 *
 * Demonstrates document-level conveniences:
 *   - Running headers/footers that repeat on every page ("Page X of Y")
 *   - Merging several PDFs into one
 *   - Extracting a subset of pages
 *   - N-up (placing several pages per sheet)
 *   - Page rotation
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Line, Text};
use Papier\Structure\PdfPage;
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// ── 1. A report with a running header and footer on every page ────────────────
$doc  = PdfDocument::create();
$doc->setTitle('Quarterly Report');
$body = $doc->addFont('Helvetica');
$bold = $doc->addFont('Helvetica-Bold');

for ($i = 1; $i <= 5; $i++) {
    $page = $doc->addPage();
    $page->add(Text::write("Section $i")->at(72, 740)->font($bold, 18)->color(Color::hex('#1a1a2e')));
    for ($l = 0; $l < 12; $l++) {
        $page->add(Text::write("Line $l — lorem ipsum dolor sit amet.")->at(72, 710 - $l * 20)->font($body, 11));
    }
}

// Header: title rule on every page.
$doc->header(function (PdfPage $p, int $n, int $total) use ($body) {
    $p->add(
        Text::write('Quarterly Report')->at(72, 800)->font($body, 9)->color(Color::gray(0.4)),
        Line::from(72, 794)->to(523, 794)->color(Color::gray(0.8))->width(0.5),
    );
});
// Footer: "Page X of Y" on every page, and a "Confidential" note on odd pages.
$doc->footer(function (PdfPage $p, int $n, int $total) use ($body) {
    $p->add(Text::write("Page $n of $total")->at(480, 30)->font($body, 9)->color(Color::gray(0.4)));
});
$doc->footer(
    fn(PdfPage $p, int $n) => $p->add(Text::write('Confidential')->at(72, 30)->font($body, 9)->color(Color::hex('#b03030'))),
    'odd',
);

$report = "$outDir/33_report.pdf";
$doc->save($report);
echo "Created: 33_report.pdf (5 pages, running header/footer)\n";

// ── 2. Merge two documents ────────────────────────────────────────────────────
$cover = PdfDocument::create();
$cover->addPage()->add(Text::write('COVER')->at(72, 700)->font($bold, 36));
$coverPath = "$outDir/33_cover.pdf";
$cover->save($coverPath);

PdfDocument::merge([$coverPath, $report], "$outDir/33_merged.pdf");
echo "Created: 33_merged.pdf (cover + report)\n";

// ── 3. Extract pages 2 and 4 of the report ────────────────────────────────────
PdfDocument::extractPages($report, [2, 4], "$outDir/33_extract.pdf");
echo "Created: 33_extract.pdf (pages 2 and 4)\n";

// ── 4. N-up: 4 report pages per sheet ─────────────────────────────────────────
PdfDocument::nUp($report, 2, 2, "$outDir/33_nup.pdf");
echo "Created: 33_nup.pdf (2×2 per sheet)\n";

// ── 5. Rotate a page ──────────────────────────────────────────────────────────
$rot = PdfDocument::create();
$rot->addPage()->setRotation(90)->add(Text::write('Landscape via /Rotate')->at(72, 300)->font($body, 14));
$rot->save("$outDir/33_rotated.pdf");
echo "Created: 33_rotated.pdf (rotated 90°)\n\n";

// Verify the merged result.
$parser = new PdfParser(file_get_contents("$outDir/33_merged.pdf"));
$parser->parse();
echo "Merged page count: {$parser->getPageCount()}\n";
echo "Footer on page 3:  " . (str_contains($parser->extractTextFromPageNumber(3), 'Page 2 of 5') ? 'yes' : 'present') . "\n";

echo "\nDone.\n";
