<?php

/**
 * Example 18: Incremental updates (ISO 32000-1 §7.5.6)
 *
 * Appends a new revision to an existing PDF without rewriting it. The original
 * bytes are preserved verbatim; changed/added objects, a new cross-reference
 * section, and a trailer with /Prev are appended. This is the mechanism used
 * by digital signatures and by tools that edit files they did not author.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Metadata\DocumentInfo;
use Papier\Objects\{PdfDictionary, PdfName, PdfString};
use Papier\Parser\PdfParser;
use Papier\Writer\IncrementalUpdater;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// ── 1. Create an original document ───────────────────────────────────────────
$doc  = PdfDocument::create();
$doc->setTitle('Draft v1')->setAuthor('Papier');
$font = $doc->addFont('Helvetica');
$page = $doc->addPage();
$page->add(Text::write('Original content — revision 1')->at(72, 750)->font($font, 14));

$original = "$outDir/18_original.pdf";
$doc->save($original);
echo "Created: 18_original.pdf  (" . number_format(filesize($original)) . " bytes)\n";

// ── 2. Open it for incremental update ─────────────────────────────────────────
$updater = PdfDocument::openForUpdate($original);

// Locate the Info dictionary object and rewrite the title in a new revision.
$parser  = new PdfParser(file_get_contents($original));
$parser->parse();
$infoNum = $parser->getXref()->getInfoObjectNumber();

$info = new DocumentInfo();
$info->setTitle('Draft v2 (updated in place)')->setAuthor('Papier');
$updater->updateObject($infoNum, $info->getDictionary());

// Also add a brand-new object in the appended revision.
$marker = new PdfDictionary();
$marker->set('Type', new PdfName('RevisionMarker'));
$marker->set('Note', new PdfString('Added without rewriting the file.'));
$updater->addObject($marker);

$updatedFile = "$outDir/18_updated.pdf";
$updater->save($updatedFile);
echo "Created: 18_updated.pdf   (" . number_format(filesize($updatedFile)) . " bytes)\n";

// ── 3. Prove the update is truly incremental ──────────────────────────────────
$origBytes = file_get_contents($original);
$updBytes  = file_get_contents($updatedFile);

echo "\nOriginal bytes preserved as prefix: "
   . (str_starts_with($updBytes, $origBytes) ? 'yes' : 'no') . "\n";
echo "Number of revisions (%%EOF markers): " . substr_count($updBytes, '%%EOF') . "\n";

$reparsed = new PdfParser($updBytes);
$reparsed->parse();
echo "Title after update: {$reparsed->getTitle()}\n";
echo "Original page still present: " . ($reparsed->getPageCount() === 1 ? 'yes' : 'no') . "\n";

echo "\nDone.\n";
