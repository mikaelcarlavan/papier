<?php

/**
 * Example 07: Bookmarks (outlines) and Encryption
 *
 * Creates a multi-page document with a hierarchical bookmark tree and
 * demonstrates password protection with permission flags.
 *
 * Page decoration (headers, body text) uses the elements API.
 * Outline/bookmark registration and encryption setup use their own APIs.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Rectangle, Text};
use Papier\Encryption\StandardSecurityHandler;
use Papier\Structure\{PdfOutline, PdfOutlineItem};

// ── Part 1: Bookmarked multi-page document ────────────────────────────────────

$doc = PdfDocument::create();
$doc->setTitle('Bookmarks & Encryption Demo')
    ->setAuthor('Papier Library')
    ->setSubject('Demonstrates document outlines and encryption');

$font     = $doc->addFont('Helvetica',        'F1');
$fontBold = $doc->addFont('Helvetica-Bold',   'F2');
$fontItal = $doc->addFont('Helvetica-Oblique','F3');

$chapters = [
    'Introduction'        => 'This chapter introduces the document.',
    'Chapter 1: Basics'   => 'PDF basics and file structure.',
    'Chapter 2: Text'     => 'Text and font handling in PDF.',
    'Chapter 3: Graphics' => 'Vector graphics and colour spaces.',
    'Chapter 4: Images'   => 'Raster image embedding.',
    'Conclusion'          => 'Summary and further reading.',
];

$outline = new PdfOutline();

$pageNum = 1;
foreach ($chapters as $title => $body) {
    $page = $doc->addPage();

    // Chapter header banner
    $page->add(
        Rectangle::create(0, 780, 595, 60)->fill(Color::rgb(0.2, 0.3, 0.6)),
        Text::write($title)->at(72, 803)->font($fontBold, 20)->color(Color::white()),
        Text::write("Page {$pageNum}")->at(510, 803)->font($font, 9)->color(Color::rgb(0.8, 0.8, 0.8)),
        Text::write($body)->at(72, 750)->font($font, 12)->color(Color::black()),
    );

    // Sample section lines
    for ($i = 1; $i <= 5; $i++) {
        $page->add(
            Text::write("Section {$pageNum}.{$i}: Sample content paragraph number {$i}.")
                ->at(72, 750 - $i * 20)->font($fontItal, 11)->color(Color::rgb(0.1, 0.1, 0.4)),
        );
    }

    // Create bookmark
    $item = new PdfOutlineItem($title);
    if (str_starts_with($title, 'Chapter')) {
        $item->setBold(true);
        $item->setColor(0.2, 0.3, 0.6);

        for ($s = 1; $s <= 3; $s++) {
            $sub = new PdfOutlineItem("Section {$pageNum}.{$s}");
            $item->addChild($sub);
        }
    }
    $outline->addItem($item);
    $pageNum++;
}

$doc->setOutline($outline);

// ── Part 2: Set viewer preferences ───────────────────────────────────────────
$doc->setViewerPreferences([
    'HideToolbar'           => false,
    'HideMenubar'           => false,
    'FitWindow'             => false,
    'DisplayDocTitle'       => true,
    'PrintScaling'          => 'None',
    'NonFullScreenPageMode' => 'UseOutlines',
]);

$doc->save(__DIR__ . '/output/07_bookmarks.pdf');
echo "Created: 07_bookmarks.pdf\n";

// ── Part 3: Encrypted document ────────────────────────────────────────────────
$docEnc = PdfDocument::create();
$docEnc->setTitle('Encrypted PDF')
       ->setAuthor('Papier Library');

$fontE = $docEnc->addFont('Helvetica',      'F1');
$fontB = $docEnc->addFont('Helvetica-Bold', 'F2');

$pageE = $docEnc->addPage();

$pageE->add(
    Rectangle::create(0, 0, 595, 841)->fill(Color::rgb(0.15, 0.15, 0.15)),
    Text::write('This PDF is encrypted!')->at(72, 700)->font($fontB, 36)->color(Color::rgb(0.9, 0.7, 0.1)),
    Text::write('User password: "user"')->at(72, 660)->font($fontE, 14)->color(Color::rgb(0.8, 0.8, 0.8)),
    Text::write('Owner password: "owner"')->at(72, 640)->font($fontE, 14)->color(Color::rgb(0.8, 0.8, 0.8)),
    Text::write('Permissions: Print and Copy are DISABLED.')->at(72, 600)->font($fontE, 12)->color(Color::rgb(0.6, 0.6, 0.6)),
    Text::write('Algorithm: AES-128 (V=4, R=4)')->at(72, 570)->font($fontE, 11)->color(Color::rgb(0.5, 0.5, 0.5)),
);

// Encrypt: allow print+view but no copy, no modify
$permissions = StandardSecurityHandler::PERM_PRINT
             | StandardSecurityHandler::PERM_FILL_FORM
             | StandardSecurityHandler::PERM_EXTRACT;

$docEnc->encrypt(
    userPassword:  'user',
    ownerPassword: 'owner',
    permissions:   $permissions,
    algorithm:     StandardSecurityHandler::AES_128,
);

$docEnc->save(__DIR__ . '/output/07_encrypted.pdf');
echo "Created: 07_encrypted.pdf  (user pw: 'user', owner pw: 'owner')\n";
