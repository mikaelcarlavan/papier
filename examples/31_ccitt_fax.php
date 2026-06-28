<?php

/**
 * Example 31: CCITT Group 4 (fax) image compression
 *
 * Builds a bilevel (1-bit) image, compresses it with CCITT Group 4, and embeds
 * it as an image XObject filtered with /CCITTFaxDecode — the encoding used for
 * scanned black-and-white documents. The library can both encode and decode it.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Filter\CCITTFaxDecode;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfStream};

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$cols = 240;
$rows = 160;

// Build a bilevel bitmap (bit 1 = white, 0 = black): a border + diagonal + squares.
$grid = [];
for ($y = 0; $y < $rows; $y++) {
    $row = [];
    for ($x = 0; $x < $cols; $x++) {
        $border   = $x < 4 || $x >= $cols - 4 || $y < 4 || $y >= $rows - 4;
        $diagonal = abs(($x * $rows) - ($y * $cols)) < ($cols * 2);
        $square   = ($x % 40 < 20) && ($y % 40 < 20);
        $black    = $border || $diagonal || $square;
        $row[]    = $black ? 0 : 1;
    }
    $grid[] = $row;
}

// Pack to 1bpp (MSB first).
$raw = '';
foreach ($grid as $row) {
    for ($i = 0; $i < $cols; $i += 8) {
        $byte = 0;
        for ($j = 0; $j < 8; $j++) { $byte = ($byte << 1) | ($row[$i + $j] ?? 1); }
        $raw .= chr($byte);
    }
}

// Compress with CCITT Group 4.
$params = new PdfDictionary();
$params->set('K', new PdfInteger(-1));
$params->set('Columns', new PdfInteger($cols));
$params->set('Rows', new PdfInteger($rows));

$codec   = new CCITTFaxDecode();
$encoded = $codec->encode($raw, $params);

echo 'Raw 1bpp size:      ' . number_format(strlen($raw)) . " bytes\n";
echo 'CCITT G4 size:      ' . number_format(strlen($encoded)) . " bytes\n";
printf("Compression ratio:  %.1f×\n", strlen($raw) / max(1, strlen($encoded)));
echo 'Round-trip decode:  ' . ($codec->decode($encoded, $params) === $raw ? 'lossless ✓' : 'MISMATCH ✗') . "\n\n";

// Embed as a CCITT-filtered image XObject.
$img = new PdfStream();
$d = $img->getDictionary();
$d->set('Type', new PdfName('XObject'));
$d->set('Subtype', new PdfName('Image'));
$d->set('Width', new PdfInteger($cols));
$d->set('Height', new PdfInteger($rows));
$d->set('BitsPerComponent', new PdfInteger(1));
$d->set('ColorSpace', new PdfName('DeviceGray'));
$d->set('Filter', new PdfName('CCITTFaxDecode'));
$d->set('DecodeParms', $params);
$img->setData($encoded); // already CCITT-encoded; do not re-compress

$doc  = PdfDocument::create();
$doc->setTitle('CCITT fax image');
$page = $doc->addPage();
$page->getResources()->addXObject('Fax', $img);

$cs = new ContentStream();
$cs->save()->transform(360, 0, 0, 240, 100, 480)->drawXObject('Fax')->restore();
$page->addContent($cs);

$file = "$outDir/31_ccitt_fax.pdf";
$doc->save($file);
echo "Created: 31_ccitt_fax.pdf (" . number_format(filesize($file)) . " bytes)\n";

echo "\nDone.\n";
