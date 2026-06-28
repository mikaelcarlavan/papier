<?php

/**
 * Example 17: Compressed object streams + cross-reference stream (PDF 1.5+)
 *
 * Demonstrates useObjectStreams(), which packs non-stream objects into
 * compressed object streams (/ObjStm) and writes a cross-reference stream
 * (/XRef) instead of a classic table — producing a noticeably smaller file.
 * The parser reads either form transparently.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Text};
use Papier\Parser\PdfParser;

function buildReport(bool $compressed): string
{
    $doc = PdfDocument::create();
    $doc->setTitle('Quarterly Report')->setAuthor('Papier');
    $font = $doc->addFont('Helvetica');
    $bold = $doc->addFont('Helvetica-Bold');

    for ($p = 1; $p <= 8; $p++) {
        $page = $doc->addPage();
        $page->add(
            Text::write("Section $p")->at(72, 780)->font($bold, 20)->color(Color::hex('#1a1a2e')),
        );
        for ($line = 0; $line < 30; $line++) {
            $y = 740 - $line * 22;
            $page->add(Text::write("Row $line — lorem ipsum dolor sit amet, consectetur.")
                ->at(72, $y)->font($font, 11));
        }
    }

    $doc->attachFile('summary.txt', str_repeat("data row\n", 200), 'text/plain');

    if ($compressed) {
        $doc->useObjectStreams();
    }
    return $doc->toString();
}

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$classic    = buildReport(false);
$compressed = buildReport(true);

file_put_contents("$outDir/17_classic.pdf", $classic);
file_put_contents("$outDir/17_compressed.pdf", $compressed);

$saving = 100 * (1 - strlen($compressed) / strlen($classic));

echo "Created: 17_classic.pdf     (" . number_format(strlen($classic)) . " bytes)\n";
echo "Created: 17_compressed.pdf  (" . number_format(strlen($compressed)) . " bytes)\n";
printf("Size reduction: %.1f%%\n\n", $saving);

// Both read back identically.
$parser = new PdfParser($compressed);
$parser->parse();
echo "Re-read compressed file:\n";
echo "  Title:      {$parser->getTitle()}\n";
echo "  Pages:      {$parser->getPageCount()}\n";
echo "  Attachment: {$parser->getAttachments()[0]['name']}"
   . " (" . strlen($parser->getAttachments()[0]['data']) . " bytes)\n";

echo "\nDone.\n";
