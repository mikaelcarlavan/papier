<?php

/**
 * Example 19: Reading encrypted PDFs + full read-side parity
 *
 * Demonstrates:
 *   - Decrypting an existing password-protected PDF (RC4 / AES-128 / AES-256)
 *   - Reading the outline (bookmark) tree
 *   - Reading AcroForm field values
 *   - Reading embedded file attachments
 *   - Reading the XMP metadata packet
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Encryption\{EncryptionAlgorithm, StandardSecurityHandler};
use Papier\AcroForm\{AcroForm, TextField};
use Papier\Structure\{PdfOutline, PdfOutlineItem};
use Papier\Objects\PdfString;
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// ── Build a rich, encrypted document ──────────────────────────────────────────
$doc  = PdfDocument::create();
$doc->setTitle('Confidential Memo')->setAuthor('Papier')->setSubject('Demo');
$font = $doc->addFont('Helvetica');
$page = $doc->addPage();
$page->add(Text::write('Confidential — résumé enclosed, for authorised eyes only.')->at(72, 750)->font($font, 14));

// Outline
$outline = new PdfOutline();
$intro = new PdfOutlineItem('Introduction');
$intro->addChild(new PdfOutlineItem('Background'));
$outline->addItem($intro);
$outline->addItem(new PdfOutlineItem('Conclusion'));
$doc->setOutline($outline);

// Form field
$form = new AcroForm();
$nameField = new TextField('reviewer', 'reviewer');
$nameField->setRect(200, 700, 400, 718)->setDefaultAppearance($font, 12)
          ->setValue(PdfString::text('Jane Doe'));
$form->addField($nameField);
$page->addFormField($nameField);
$doc->setAcroForm($form);

// Attachment
$doc->attachFile('notes.txt', "internal review notes\nline 2", 'text/plain');

// Encrypt with AES-256.
$doc->encrypt('open-sesame', 'owner-key', StandardSecurityHandler::PERM_ALL, EncryptionAlgorithm::Aes_256);

$encFile = "$outDir/19_encrypted.pdf";
$doc->save($encFile);
echo "Created: 19_encrypted.pdf (AES-256, user password 'open-sesame')\n\n";

// ── Open it with the password and read everything back ────────────────────────
$parser = (new PdfParser(file_get_contents($encFile)))->setPassword('open-sesame');
$parser->parse();

echo "Encrypted: " . ($parser->isEncrypted() ? 'yes' : 'no') . "\n";
echo "Title:     {$parser->getTitle()}\n";
echo "Text:      " . trim($parser->extractText()) . "\n\n";

echo "--- Outline ---\n";
$printOutline = function (array $items, int $depth = 0) use (&$printOutline) {
    foreach ($items as $item) {
        echo str_repeat('  ', $depth) . "• {$item['title']}\n";
        if (!empty($item['children'])) {
            $printOutline($item['children'], $depth + 1);
        }
    }
};
$printOutline($parser->getOutlines());

echo "\n--- Form fields ---\n";
foreach ($parser->getFormFields() as $f) {
    echo "  {$f['name']} ({$f['type']}) = {$f['value']}\n";
}

echo "\n--- Attachments ---\n";
foreach ($parser->getAttachments() as $a) {
    echo "  {$a['name']} [{$a['mime']}], " . strlen($a['data']) . " bytes\n";
}

echo "\n--- XMP metadata ---\n";
$xmp = $parser->getXmpMetadata();
echo $xmp !== null ? "  present (" . strlen($xmp) . " bytes)\n" : "  none\n";

// Owner password also opens the file.
$asOwner = (new PdfParser(file_get_contents($encFile)))->setPassword('owner-key');
$asOwner->parse();
echo "\nOpened with owner password: " . ($asOwner->getTitle() === 'Confidential Memo' ? 'ok' : 'failed') . "\n";

echo "\nDone.\n";
