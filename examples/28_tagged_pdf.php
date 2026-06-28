<?php

/**
 * Example 28: Tagged PDF / accessibility (ISO 32000-1 §14.7)
 *
 * Tagged PDF links visible content to a logical structure tree via
 * marked-content identifiers (MCIDs), enabling screen readers, reflow, and the
 * accessible PDF/A-2a / PDF/UA conformance levels.
 *
 * The flow:
 *   1. Wrap each piece of content in a marked-content sequence with an MCID.
 *   2. Build a structure tree whose elements claim those MCIDs.
 *   The writer then emits /MarkInfo, /StructTreeRoot, a /ParentTree, and
 *   per-page /StructParents automatically.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\Text;
use Papier\LogicalStructure\{StructElement, StructTreeRoot};
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$doc  = PdfDocument::create();
$doc->setTitle('Accessible Report')->enablePdfA(2, 'A'); // PDF/A-2a (accessible)
$font = $doc->addFont(__DIR__ . '/Lato-Regular.ttf', '', subset: true); // embedded font required
$page = $doc->addPage();

// 1. Author content as tagged marked-content sequences.
$cs = new ContentStream();
$cs->setFontResolver(fn (string $n) => $doc->getWriter()->getFontMetrics($n));

$cs->beginMarkedContentMcid('H1', 0)
   ->beginText()->setFont($font, 24)->setTextPosition(72, 760)->showText('Quarterly Report')->endText()
   ->endMarkedContent();

$cs->beginMarkedContentMcid('P', 1)
   ->beginText()->setFont($font, 12)->setTextPosition(72, 720)
   ->showText('This paragraph is tagged so assistive technology can read it.')->endText()
   ->endMarkedContent();

$cs->beginMarkedContentMcid('P', 2)
   ->beginText()->setFont($font, 12)->setTextPosition(72, 700)
   ->showText('Each span is claimed by a structure element.')->endText()
   ->endMarkedContent();

$page->addContent($cs);

// 2. Build the structure tree: Document → H1, P, P (claiming MCIDs 0,1,2).
$tree    = new StructTreeRoot();
$docElem = new StructElement('Document');

$h1 = new StructElement('H1');
$h1->addMCID(0, $page->getDictionary());

$p1 = new StructElement('P');
$p1->addMCID(1, $page->getDictionary());

$p2 = new StructElement('P');
$p2->addMCID(2, $page->getDictionary());

$docElem->addChild($h1)->addChild($p1)->addChild($p2);
$tree->addChild($docElem);
$doc->setStructTree($tree);

$file = "$outDir/28_tagged_pdf.pdf";
$doc->save($file);
echo "Created: 28_tagged_pdf.pdf (" . number_format(filesize($file)) . " bytes)\n";

// Verify the tagging round-trips.
$parser = new PdfParser(file_get_contents($file));
$parser->parse();
echo "Tagged (MarkInfo/Marked):  " . ($parser->isTagged() ? 'yes' : 'no') . "\n";
echo "Has StructTreeRoot:        " . ($parser->getStructTreeRoot() !== null ? 'yes' : 'no') . "\n";
$root = $parser->getStructTreeRoot();
echo "Has ParentTree:            " . ($root && $root->get('ParentTree') !== null ? 'yes' : 'no') . "\n";
echo "PDF/A conformance level:   " . (preg_match('/<pdfaid:conformance>(\w)/', (string) $parser->getXmpMetadata(), $m) ? $m[1] : '?') . "\n";

echo "\nDone.\n";
