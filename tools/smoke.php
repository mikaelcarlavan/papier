<?php

/**
 * Smoke test — exercises the library end to end without dev dependencies.
 *
 *     php tools/smoke.php
 *
 * PHPUnit 11 requires PHP 8.2, but the library supports 8.1. This script runs
 * on 8.1 with production dependencies only, so the lowest supported version is
 * covered by something that actually builds and re-reads a document rather
 * than only by a version constraint in composer.json.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\Elements\Color;
use Papier\Elements\Rectangle;
use Papier\Elements\Table;
use Papier\Elements\Text;
use Papier\Elements\TextBox;
use Papier\Parser\PdfParser;
use Papier\PdfDocument;

$failures = [];

/** Assert a condition, collecting failures rather than stopping at the first. */
function check(string $what, bool $ok, string $detail = ''): void
{
    global $failures;

    if ($ok) {
        echo "  ok    {$what}\n";
        return;
    }

    $failures[] = $what . ($detail !== '' ? " ({$detail})" : '');
    echo "  FAIL  {$what}" . ($detail !== '' ? " — {$detail}" : '') . "\n";
}

echo 'Smoke test on PHP ' . PHP_VERSION . "\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// Build a document exercising the main subsystems
// ─────────────────────────────────────────────────────────────────────────────

echo "Writing:\n";

$doc = PdfDocument::create();
$doc->setTitle('Smoke Test')
    ->setAuthor('CI')
    ->setSubject('End-to-end check');

$regular = $doc->addFont('Helvetica');
$bold    = $doc->addFont('Helvetica-Bold');

$page = $doc->addPage();

$page->add(
    Rectangle::create(0, 780, 595.28, 60)->fill(Color::hex('#2c3e7a')),
    Text::write('Smoke Test')->at(40, 806)->font($bold, 22)->color(Color::white()),
    Text::write('Accented text: éàüñ')->at(40, 740)->font($regular, 12),
    TextBox::write('A paragraph long enough that the text box has to wrap it across '
        . 'more than one line, which exercises the width measurement path.')
        ->at(40, 700)->size(500)->font($regular, 11)->lineHeight(1.4),
);

$table = Table::create(40, 620)
    ->setColumnWidths(200, 120, 120)
    ->setFont($regular, 10, 'Helvetica')
    ->setHeaderFont($bold, 10, 'Helvetica-Bold')
    ->setHeaderRows(1);

$table->addRow(['Item', 'Quantity', 'Amount']);
$table->addRow(['First', '2', '19.90']);
$table->addRow(['Second', '5', '44.50']);

$page->add($table);

$bytes = $doc->toString();

check('document produced bytes', $bytes !== '', 'toString() returned an empty string');
check('starts with a PDF header', str_starts_with($bytes, '%PDF-'), 'header: ' . substr($bytes, 0, 8));
check('ends with the EOF marker', str_contains(substr($bytes, -32), '%%EOF'));
check('is a plausible size', strlen($bytes) > 1000, strlen($bytes) . ' bytes');

// ─────────────────────────────────────────────────────────────────────────────
// Read it back
// ─────────────────────────────────────────────────────────────────────────────

echo "\nReading back:\n";

$parser = new PdfParser($bytes);
$parser->parse();

check('page count is 1', $parser->getPageCount() === 1, 'got ' . $parser->getPageCount());

$metadata = $parser->getMetadata();
check('title round-trips', ($metadata['title'] ?? null) === 'Smoke Test', 'got ' . var_export($metadata['title'] ?? null, true));

$text = $parser->extractText();
check('extracted text contains the heading', str_contains($text, 'Smoke Test'));
check('extracted text contains accented characters', str_contains($text, 'éàüñ'));
check('extracted text contains a table cell', str_contains($text, 'Second'));

// ─────────────────────────────────────────────────────────────────────────────

echo "\n";

if ($failures) {
    echo count($failures) . " check(s) failed:\n";
    foreach ($failures as $failure) {
        echo "  - {$failure}\n";
    }
    exit(1);
}

echo "All smoke checks passed.\n";
