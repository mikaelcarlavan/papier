<?php

/**
 * Example 05: Interactive AcroForm
 *
 * Creates a PDF with text fields, checkboxes, radio buttons, a combo box,
 * a list box, and a submit button — a fully functional PDF form.
 *
 * Page decoration (header, labels, rules) uses the elements API.
 * AcroForm fields are registered through the AcroForm API as before.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\AcroForm\{AcroForm, TextField, CheckBoxField, ComboBoxField, ListBoxField, PushButtonField};
use Papier\Elements\{Color, Line, Rectangle, Text};
use Papier\Objects\PdfDictionary;

$doc  = PdfDocument::create();
$doc->setTitle('AcroForm Example');
$f1 = $doc->addFont('Helvetica');
$f2 = $doc->addFont('Helvetica-Bold');

$page = $doc->addPage();

// ── Page header ───────────────────────────────────────────────────────────────
$page->add(
    Rectangle::create(0, 780, 595, 61)->fill(Color::rgb(0.2, 0.3, 0.6)),
    Text::write('Papier PDF — Interactive Form')
        ->at(72, 800)->font($f2, 22)->color(Color::white()),
);

// ── Section: Personal Info ────────────────────────────────────────────────────
$page->add(
    Text::write('Personal Information')->at(72, 755)->font($f2, 13)->color(Color::rgb(0.2, 0.3, 0.6)),
    Line::from(72, 750)->to(523, 750)->color(Color::rgb(0.2, 0.3, 0.6))->width(0.5),

    Text::write('First Name:')    ->at(72,  730)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
    Text::write('Last Name:')     ->at(310, 730)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
    Text::write('Email Address:') ->at(72,  700)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
    Text::write('Phone Number:')  ->at(72,  670)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
);

// ── Section: Preferences ─────────────────────────────────────────────────────
$page->add(
    Text::write('Preferences')->at(72, 640)->font($f2, 13)->color(Color::rgb(0.2, 0.3, 0.6)),
    Line::from(72, 635)->to(523, 635)->color(Color::rgb(0.2, 0.3, 0.6))->width(0.5),

    Text::write('Country:')   ->at(72,  615)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
    Text::write('Language:')  ->at(310, 615)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
    Text::write('Newsletter:')->at(72,  580)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
    Text::write('Interests:') ->at(72,  548)->font($f1, 10)->rgb(0.2, 0.2, 0.2),
);

// ── Section: Comments ─────────────────────────────────────────────────────────
$page->add(
    Text::write('Comments')->at(72, 505)->font($f2, 13)->color(Color::rgb(0.2, 0.3, 0.6)),
    Line::from(72, 500)->to(523, 500)->color(Color::rgb(0.2, 0.3, 0.6))->width(0.5),
);

// ── Build AcroForm ────────────────────────────────────────────────────────────
$form = new AcroForm();
$form->setNeedAppearances(true);
$form->setDefaultAppearance($f1, 10);

$defaultResources = new PdfDictionary();
$defaultResources->set('Font', new PdfDictionary());
$form->setDefaultResources($defaultResources);

$addText = function (string $name, float $x1, float $y1, float $x2, float $y2)
    use ($form, $page, $f1): TextField
{
    $field = new TextField($name, $name);
    $field->setDefaultAppearance($f1, 11);
    $field->setRect($x1, $y1, $x2, $y2);
    $form->addField($field);
    $page->addFormField($field);
    return $field;
};

$addText('FirstName', 140, 720, 290, 740);
$addText('LastName',  375, 720, 523, 740);
$addText('Email',     140, 690, 523, 710);
$addText('Phone',     140, 660, 300, 680);

// Country — combo box
$countryField = new ComboBoxField('Country', 'Country');
$countryField->addOption('US', 'United States')
             ->addOption('GB', 'United Kingdom')
             ->addOption('FR', 'France')
             ->addOption('DE', 'Germany')
             ->addOption('JP', 'Japan')
             ->buildOpt();
$countryField->setDefaultAppearance($f1, 10);
$countryField->setRect(140, 600, 280, 622);
$form->addField($countryField);
$page->addFormField($countryField);

// Language — list box
$langField = new ListBoxField('Language', 'Language');
$langField->addOption('en', 'English')
          ->addOption('fr', 'French')
          ->addOption('de', 'German')
          ->addOption('es', 'Spanish')
          ->addOption('ja', 'Japanese')
          ->setMultiSelect(false)
          ->buildOpt();
$langField->setDefaultAppearance($f1, 10);
$langField->setRect(375, 590, 523, 635);
$form->addField($langField);
$page->addFormField($langField);

// Newsletter — checkbox
$newsField = new CheckBoxField('Newsletter', 'Newsletter');
$newsField->setRect(140, 568, 155, 583);
$form->addField($newsField);
$page->addFormField($newsField);
$page->add(Text::write('Yes, send me the newsletter')->at(160, 570)->font($f1, 10));

// Interests — checkboxes
$interestItems = ['PHP' => 72, 'PDF' => 140, 'Typography' => 208, 'OpenSource' => 310];
foreach ($interestItems as $cbName => $x) {
    $cbField = new CheckBoxField("Interest_{$cbName}", $cbName);
    $cbField->setRect($x, 540, $x + 14, 554);
    $form->addField($cbField);
    $page->addFormField($cbField);
    $page->add(Text::write($cbName)->at($x + 16, 542)->font($f1, 9));
}

// Comments — multiline
$commentField = new TextField('Comments', 'Comments');
$commentField->setMultiline(true)->setDefaultAppearance($f1, 10);
$commentField->setRect(72, 430, 523, 492);
$form->addField($commentField);
$page->addFormField($commentField);

// Submit button
$submitBtn = new PushButtonField('Submit', 'Submit');
$submitBtn->setRect(310, 380, 430, 405);
$form->addField($submitBtn);
$page->addFormField($submitBtn);
$page->add(
    Rectangle::create(310, 380, 120, 25)->fill(Color::rgb(0.2, 0.5, 0.9)),
    Text::write('Submit Form')->at(335, 390)->font($f2, 12)->color(Color::white()),
);

// Reset button
$resetBtn = new PushButtonField('Reset', 'Reset');
$resetBtn->setRect(440, 380, 523, 405);
$form->addField($resetBtn);
$page->addFormField($resetBtn);
$page->add(
    Rectangle::create(440, 380, 83, 25)->fill(Color::rgb(0.7, 0.2, 0.2)),
    Text::write('Reset')->at(455, 390)->font($f2, 12)->color(Color::white()),
);

$doc->setAcroForm($form);
$doc->save(__DIR__ . '/output/05_forms.pdf');
echo "Created: 05_forms.pdf\n";
