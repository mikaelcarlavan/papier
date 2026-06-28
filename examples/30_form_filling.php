<?php

/**
 * Example 30: Filling an existing PDF form
 *
 * Open a PDF containing an AcroForm, set field values, and save via an
 * incremental update. Text fields get a regenerated appearance stream so the
 * value shows in every viewer; check boxes set their on/off state.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\AcroForm\{AcroForm, FormFiller, TextField, CheckBoxField};
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// 1. Create a blank form (the "template").
$doc  = PdfDocument::create();
$doc->setTitle('Registration Form');
$font = $doc->addFont('Helvetica');
$page = $doc->addPage();
$page->add(
    Text::write('Registration')->at(72, 770)->font($font, 18),
    Text::write('Name:')->at(72, 703)->font($font, 12),
    Text::write('Email:')->at(72, 673)->font($font, 12),
    Text::write('Subscribe to newsletter:')->at(72, 643)->font($font, 12),
);

$form = new AcroForm();
foreach (['name' => 700, 'email' => 670] as $fname => $y) {
    $tf = new TextField($fname, $fname);
    $tf->setRect(200, $y, 420, $y + 18)->setDefaultAppearance($font, 12);
    $form->addField($tf);
    $page->addFormField($tf);
}
$cb = new CheckBoxField('subscribe', 'subscribe');
$cb->setRect(230, 643, 244, 657);
$form->addField($cb);
$page->addFormField($cb);
$doc->setAcroForm($form);

$blank = "$outDir/30_form_blank.pdf";
$doc->save($blank);
echo "Created: 30_form_blank.pdf\n";

// 2. Fill it.
$filler = new FormFiller(file_get_contents($blank));
echo "Fields found: " . implode(', ', $filler->getFieldNames()) . "\n";

$filler->setText('name', 'Alice Example')
       ->setText('email', 'alice@example.com')
       ->setCheckbox('subscribe', true);

$filled = "$outDir/30_form_filled.pdf";
$filler->saveAs($filled);
echo "Created: 30_form_filled.pdf\n\n";

// 3. Read the values back.
$parser = new PdfParser(file_get_contents($filled));
$parser->parse();
echo "--- Filled values ---\n";
foreach ($parser->getFormFields() as $f) {
    echo "  {$f['name']} = {$f['value']}\n";
}

echo "\nDone.\n";
