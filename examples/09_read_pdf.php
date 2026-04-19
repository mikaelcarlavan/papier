<?php

/**
 * Example 09: Reading an existing PDF
 *
 * Demonstrates using PdfParser to open and inspect a PDF:
 * - Read document metadata (title, author, etc.)
 * - Get the page count
 * - Extract text from all pages at once or by page number
 * - List fonts used in the document
 * - List annotations
 * - Extract images (name, dimensions, filter)
 * - Per-page structured info via getPageInfo()
 * - Inspect the catalog and page dictionaries
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Parser\PdfParser;
use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfString, PdfInteger, PdfReal, PdfStream};

// ── Read one of the PDFs we generated ────────────────────────────────────────
$inputFile = __DIR__ . '/output/02_text_formatting.pdf';

if (!file_exists($inputFile)) {
    echo "Run example 02 first to generate the input file.\n";
    exit(1);
}

echo "=== Reading: $inputFile ===\n\n";

$parser = PdfDocument::open($inputFile);

// ── 1. PDF version ────────────────────────────────────────────────────────────
echo "PDF Version: {$parser->getVersion()}\n";

// ── 2. Document metadata ──────────────────────────────────────────────────────
echo "\n--- Document Information ---\n";
echo "Title:   " . $parser->getTitle()   . "\n";
echo "Author:  " . $parser->getAuthor()  . "\n";
echo "Subject: " . $parser->getSubject() . "\n";
echo "Creator: " . $parser->getCreator() . "\n";

if ($info = $parser->getInfo()) {
    $keys = ['Keywords', 'Producer', 'CreationDate', 'ModDate'];
    foreach ($keys as $key) {
        $v = $info->get($key);
        if ($v !== null) {
            $val = ($v instanceof PdfString) ? $v->getValue() : $v->toString();
            echo "{$key}: {$val}\n";
        }
    }
}

// ── 3. Catalog entries ────────────────────────────────────────────────────────
echo "\n--- Document Catalog ---\n";
if ($catalog = $parser->getCatalog()) {
    foreach ($catalog->getEntries() as $key => $value) {
        $type = get_class($value);
        $type = substr($type, strrpos($type, '\\') + 1);
        echo "  /{$key} ({$type})\n";
    }
}

// ── 4. Page count (high-level) ────────────────────────────────────────────────
echo "\n--- Page count: {$parser->getPageCount()} ---\n";

// ── 5. Extract all text at once ───────────────────────────────────────────────
echo "\n--- Full text (all pages, trimmed to 300 chars) ---\n";
$allText = trim(preg_replace('/\s+/', ' ', $parser->extractText()));
echo (strlen($allText) > 300 ? substr($allText, 0, 297) . '…' : $allText) . "\n";

// ── 6. Per-page structured info ───────────────────────────────────────────────
echo "\n--- Per-page info ---\n";
for ($p = 1; $p <= $parser->getPageCount(); $p++) {
    $info = $parser->getPageInfo($p);
    $w    = $info['width'];
    $h    = $info['height'];
    $text = trim(preg_replace('/\s+/', ' ', $info['text']));
    if (strlen($text) > 120) { $text = substr($text, 0, 117) . '…'; }
    echo "\nPage {$p}:\n";
    echo '  Size: ' . round($w, 1) . ' × ' . round($h, 1) . " pt  ("
        . round($w / 72, 2) . ' × ' . round($h / 72, 2) . " inch)\n";
    echo '  Fonts:  ' . (implode(', ', $info['fontNames'])  ?: '(none)') . "\n";
    echo '  Images: ' . (implode(', ', $info['imageNames']) ?: '(none)') . "\n";
    echo "  Annots: " . count($info['annotations']) . "\n";
    echo "  Text:   $text\n";
}

// ── 7. Document-level fonts ───────────────────────────────────────────────────
echo "\n--- Fonts (document) ---\n";
foreach ($parser->getFonts() as $font) {
    echo "  {$font['name']}: {$font['subtype']} / {$font['baseFont']}"
        . ($font['encoding'] ? " [{$font['encoding']}]" : '') . "\n";
}

// ── 8. Images ─────────────────────────────────────────────────────────────────
$images = $parser->extractImages();
echo "\n--- Images: " . count($images) . " ---\n";
foreach ($images as $img) {
    $bytes = strlen($img['data']);
    echo "  p{$img['page']} / {$img['name']}: {$img['width']}×{$img['height']} px"
        . " filter={$img['filter']}  data={$bytes} bytes\n";
}

// ── 9. Annotations ────────────────────────────────────────────────────────────
$annotations = $parser->getAnnotations();
echo "\n--- Annotations: " . count($annotations) . " ---\n";
foreach ($annotations as $ann) {
    $rect     = implode(', ', array_map(fn($v) => round($v, 1), $ann['rect']));
    $contents = $ann['contents'] !== '' ? ' "' . $ann['contents'] . '"' : '';
    echo "  p{$ann['page']} [{$ann['subtype']}] rect=[$rect]$contents\n";
}

// ── 10. Cross-reference table ─────────────────────────────────────────────────
$xref    = $parser->getXref();
$entries = $xref->getEntries();
echo "\n--- Cross-Reference Table ---\n";
echo "Total objects in xref: " . count($entries) . "\n";
$inUse = array_filter($entries, fn($e) => $e['inUse']);
echo "In-use objects: " . count($inUse) . "\n";

// ── 11. Object statistics ─────────────────────────────────────────────────────
$cache = $parser->getObjectCache();
echo "\n--- Resolved Objects ---\n";
$typeCounts = [];
foreach ($cache as $obj) {
    $type = get_class($obj);
    $type = substr($type, strrpos($type, '\\') + 1);
    $typeCounts[$type] = ($typeCounts[$type] ?? 0) + 1;
}
arsort($typeCounts);
foreach ($typeCounts as $type => $count) {
    echo "  {$type}: {$count}\n";
}

echo "\nDone.\n";
