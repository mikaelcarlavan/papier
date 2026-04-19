<?php

/**
 * Example 16: Importing Pages from Existing PDFs
 *
 * Demonstrates using ImportedPage and PdfDocument::importPage() to:
 *
 *  - Open an existing PDF and embed one of its pages as a background
 *  - Overlay text (customer data, stamps, signatures) on the imported page
 *  - Repeat the process across multiple source PDFs — the mail-merge pattern
 *    used to fill in state-provided blank forms for a deal-jacket workflow
 *
 * The example is self-contained: it first generates two simple "blank form"
 * PDFs (simulating forms received from external sources), then performs the
 * merge and writes the assembled deal-jacket to output/16_import_pages.pdf.
 *
 * Run order:
 *   php examples/16_import_pages.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\Content\ContentStream;
use Papier\PdfDocument;
use Papier\Parser\ImportedPage;
use Papier\Elements\{Color, Line, Rectangle, Text};

// ── 0. Helpers ────────────────────────────────────────────────────────────────

function sectionTitle(string $text, float $y, string $font, PdfDocument $doc): \Papier\Structure\PdfPage
{
    // Not used as a real helper; just illustrates font usage inline below.
    return $doc->addPage();
}

// ── 1. Generate "blank form" PDFs that represent state-provided documents ─────

function buildCreditStatementForm(string $path): void
{
    $doc  = PdfDocument::create();
    $f    = $doc->addFont('Helvetica');
    $fb   = $doc->addFont('Helvetica-Bold');
    $page = $doc->addPage();    // Letter 8.5 × 11 in  →  612 × 792 pt
    $page->setSize(612, 792);

    // Header bar
    $page->add(
        Rectangle::create(0, 752, 612, 40)->fill(Color::rgb(0.15, 0.25, 0.50)),
        Text::write('CREDIT STATEMENT')->at(24, 769)->font($fb, 14)->color(Color::white()),
        Text::write('State of Example — Division of Motor Vehicles')
            ->at(24, 755)->font($f, 8)->color(Color::rgb(0.8, 0.85, 0.9)),
    );

    // Labelled fields (blank lines the overlay will fill in)
    $fields = [
        [24, 720, 'Applicant Full Name:'],
        [24, 698, 'Date of Birth:'],
        [24, 676, 'Social Security Number:'],
        [24, 654, 'Address:'],
        [24, 632, 'City / State / ZIP:'],
        [24, 610, 'Employer:'],
        [24, 588, 'Monthly Income:'],
        [24, 566, 'Co-Applicant Name (if any):'],
    ];

    foreach ($fields as [$x, $y, $label]) {
        $page->add(
            Text::write($label)->at($x, $y)->font($fb, 9)->color(Color::rgb(0.3, 0.3, 0.3)),
            Line::from($x + 160, $y - 2)->to(580, $y - 2)
                ->color(Color::rgb(0.6, 0.6, 0.6))->width(0.5),
        );
    }

    // Signature block
    $page->add(
        Line::from(24, 90)->to(280, 90)->color(Color::black())->width(0.75),
        Text::write('Applicant Signature')->at(24, 78)->font($f, 8)->color(Color::rgb(0.4, 0.4, 0.4)),
        Line::from(320, 90)->to(580, 90)->color(Color::black())->width(0.75),
        Text::write('Date')->at(320, 78)->font($f, 8)->color(Color::rgb(0.4, 0.4, 0.4)),
    );

    $doc->save($path);
}

function buildRetailInstallmentForm(string $path): void
{
    $doc  = PdfDocument::create();
    $f    = $doc->addFont('Helvetica');
    $fb   = $doc->addFont('Helvetica-Bold');
    $page = $doc->addPage();
    $page->setSize(612, 792);

    // Header
    $page->add(
        Rectangle::create(0, 752, 612, 40)->fill(Color::rgb(0.10, 0.40, 0.20)),
        Text::write('RETAIL INSTALLMENT CONTRACT')->at(24, 769)->font($fb, 14)->color(Color::white()),
        Text::write('State of Example — Division of Motor Vehicles')
            ->at(24, 755)->font($f, 8)->color(Color::rgb(0.75, 0.92, 0.80)),
    );

    // Vehicle section
    $page->add(
        Text::write('VEHICLE INFORMATION')->at(24, 718)->font($fb, 10)->color(Color::rgb(0.1, 0.4, 0.2)),
        Line::from(24, 714)->to(588, 714)->color(Color::rgb(0.1, 0.4, 0.2))->width(0.75),
    );

    $vFields = [
        [24, 696, 'Year / Make / Model:'],
        [24, 674, 'VIN:'],
        [24, 652, 'Stock Number:'],
        [24, 630, 'Mileage:'],
    ];

    foreach ($vFields as [$x, $y, $label]) {
        $page->add(
            Text::write($label)->at($x, $y)->font($fb, 9)->color(Color::rgb(0.3, 0.3, 0.3)),
            Line::from($x + 140, $y - 2)->to(580, $y - 2)
                ->color(Color::rgb(0.6, 0.6, 0.6))->width(0.5),
        );
    }

    // Financial terms section
    $page->add(
        Text::write('FINANCIAL TERMS')->at(24, 598)->font($fb, 10)->color(Color::rgb(0.1, 0.4, 0.2)),
        Line::from(24, 594)->to(588, 594)->color(Color::rgb(0.1, 0.4, 0.2))->width(0.75),
    );

    $fFields = [
        [24, 576, 'Sale Price:'],
        [24, 554, 'Down Payment:'],
        [24, 532, 'Amount Financed:'],
        [24, 510, 'APR:'],
        [24, 488, 'Number of Payments:'],
        [24, 466, 'Monthly Payment:'],
    ];

    foreach ($fFields as [$x, $y, $label]) {
        $page->add(
            Text::write($label)->at($x, $y)->font($fb, 9)->color(Color::rgb(0.3, 0.3, 0.3)),
            Line::from($x + 140, $y - 2)->to(580, $y - 2)
                ->color(Color::rgb(0.6, 0.6, 0.6))->width(0.5),
        );
    }

    // Signature block
    $page->add(
        Line::from(24, 90)->to(280, 90)->color(Color::black())->width(0.75),
        Text::write('Buyer Signature')->at(24, 78)->font($f, 8)->color(Color::rgb(0.4, 0.4, 0.4)),
        Line::from(320, 90)->to(580, 90)->color(Color::black())->width(0.75),
        Text::write('Date')->at(320, 78)->font($f, 8)->color(Color::rgb(0.4, 0.4, 0.4)),
    );

    $doc->save($path);
}

$outputDir = __DIR__ . '/output';
if (!is_dir($outputDir)) { mkdir($outputDir, 0755, true); }

$creditFormPath   = $outputDir . '/16_blank_credit_statement.pdf';
$installFormPath  = $outputDir . '/16_blank_retail_installment.pdf';

buildCreditStatementForm($creditFormPath);
buildRetailInstallmentForm($installFormPath);
echo "Generated blank forms.\n";

// ── 2. Customer data (simulating a database result set) ───────────────────────

$customers = [
    [
        'name'       => 'Alice R. Johnson',
        'dob'        => '03/14/1985',
        'ssn'        => 'XXX-XX-4521',
        'address'    => '742 Evergreen Terrace',
        'city'       => 'Springfield, IL  62704',
        'employer'   => 'Acme Corporation',
        'income'     => '$5,800 / month',
        'vehicle'    => '2023 Toyota Camry SE',
        'vin'        => '4T1B11HK5KU012345',
        'stock'      => 'STK-20011',
        'mileage'    => '12,450 mi',
        'price'      => '$28,995.00',
        'down'       => '$3,000.00',
        'financed'   => '$25,995.00',
        'apr'        => '6.49 %',
        'payments'   => '60',
        'monthly'    => '$505.73',
    ],
    [
        'name'       => 'Marcus T. Williams',
        'dob'        => '07/22/1979',
        'ssn'        => 'XXX-XX-8834',
        'address'    => '1600 Pennsylvania Avenue NW',
        'city'       => 'Washington, DC  20500',
        'employer'   => 'Federal Bureau of Investigation',
        'income'     => '$9,200 / month',
        'vehicle'    => '2024 Ford F-150 XLT',
        'vin'        => '1FTFW1E83MKE09876',
        'stock'      => 'STK-20048',
        'mileage'    => '3,210 mi',
        'price'      => '$49,750.00',
        'down'       => '$10,000.00',
        'financed'   => '$39,750.00',
        'apr'        => '5.25 %',
        'payments'   => '72',
        'monthly'    => '$637.18',
    ],
];

// ── 3. Deal-jacket forms list (the "iterate through a list of PDFs" step) ────

$forms = [
    $creditFormPath  => 'Credit Statement',
    $installFormPath => 'Retail Installment Contract',
];

// ── 4. Build the assembled deal-jacket ───────────────────────────────────────

$jacket = PdfDocument::create();
$jacket->setTitle('Auto Financing Deal Jacket');
$jacket->setAuthor('Dealership DMS');
$jacket->setSubject('Customer deal-jacket with overlaid data');

$font      = $jacket->addFont('Helvetica');
$fontBold  = $jacket->addFont('Helvetica-Bold');

foreach ($customers as $customer) {

    // ── Cover sheet (freshly created, not imported) ────────────────────────
    $cover = $jacket->addPage();
    $cover->setSize(612, 792);

    $cover->add(
        Rectangle::create(0, 692, 612, 100)->fill(Color::rgb(0.12, 0.20, 0.40)),

        Text::write('DEAL JACKET')
            ->at(24, 762)->font($fontBold, 28)->color(Color::white()),

        Text::write('Auto Financing Package')
            ->at(24, 735)->font($font, 14)->color(Color::rgb(0.7, 0.8, 1.0)),

        Text::write('Customer:')
            ->at(24, 660)->font($fontBold, 11)->color(Color::rgb(0.2, 0.2, 0.2)),
        Text::write($customer['name'])
            ->at(110, 660)->font($font, 11)->color(Color::rgb(0.2, 0.2, 0.2)),

        Text::write('Vehicle:')
            ->at(24, 642)->font($fontBold, 11)->color(Color::rgb(0.2, 0.2, 0.2)),
        Text::write($customer['vehicle'])
            ->at(110, 642)->font($font, 11)->color(Color::rgb(0.2, 0.2, 0.2)),

        Text::write('This package contains the following documents:')
            ->at(24, 610)->font($font, 10)->color(Color::rgb(0.4, 0.4, 0.4)),
    );

    $docNum = 1;
    foreach ($forms as $formPath => $formLabel) {
        $cover->add(
            Text::write($docNum . '.  ' . $formLabel)
                ->at(36, 610 - ($docNum * 18))->font($font, 10)->color(Color::rgb(0.2, 0.2, 0.2)),
        );
        $docNum++;
    }

    // ── Iterate through each blank form PDF ───────────────────────────────
    foreach ($forms as $formPath => $formLabel) {
        $source = PdfDocument::open($formPath);

        // Iterate through every page in that form
        for ($pageNum = 1; $pageNum <= $source->getPageCount(); $pageNum++) {
            $imported = ImportedPage::fromParser($source, $pageNum);

            // Place the blank form as the page background
            $page = $jacket->importPage($imported);

            // Overlay customer data on top — coordinates match the blank
            // form's field lines defined in buildCreditStatementForm /
            // buildRetailInstallmentForm above.
            if ($formLabel === 'Credit Statement') {
                $page->add(
                    Text::write($customer['name'])    ->at(184, 720)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                    Text::write($customer['dob'])     ->at(184, 698)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                    Text::write($customer['ssn'])     ->at(184, 676)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                    Text::write($customer['address']) ->at(184, 654)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                    Text::write($customer['city'])    ->at(184, 632)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                    Text::write($customer['employer'])->at(184, 610)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                    Text::write($customer['income'])  ->at(184, 588)->font($font, 10)->color(Color::rgb(0.05, 0.15, 0.45)),
                );
            } elseif ($formLabel === 'Retail Installment Contract') {
                $page->add(
                    Text::write($customer['vehicle'])  ->at(164, 696)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['vin'])      ->at(164, 674)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['stock'])    ->at(164, 652)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['mileage'])  ->at(164, 630)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['price'])    ->at(164, 576)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['down'])     ->at(164, 554)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['financed']) ->at(164, 532)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['apr'])      ->at(164, 510)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['payments']) ->at(164, 488)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                    Text::write($customer['monthly'])  ->at(164, 466)->font($font, 10)->color(Color::rgb(0.05, 0.35, 0.15)),
                );
            }

            // Footer stamp on every imported page
            $page->add(
                Text::write('Customer: ' . $customer['name'] . '   |   ' . $formLabel . '   |   Generated: ' . date('Y-m-d'))
                    ->at(24, 20)->font($font, 7)->color(Color::rgb(0.5, 0.5, 0.5)),
            );
        }
    }
}

// ── 5. Save ───────────────────────────────────────────────────────────────────

$outputPath = $outputDir . '/16_import_pages.pdf';
$jacket->save($outputPath);

$pageCount = PdfDocument::open($outputPath)->getPageCount();
$fileSize  = number_format(filesize($outputPath));

echo "Created: 16_import_pages.pdf\n";
echo "Pages  : {$pageCount}  (" . count($customers) . " customers × " . (1 + count($forms)) . " docs each)\n";
echo "Size   : {$fileSize} bytes\n";
