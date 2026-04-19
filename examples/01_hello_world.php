<?php

/**
 * Example 01: Hello World
 *
 * Creates the simplest possible PDF: one A4 page with the text "Hello, World!"
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Line, Text};

$doc = PdfDocument::create();
$doc->setTitle('Hello World')
    ->setAuthor('Papier PDF Library')
    ->setSubject('Hello World example');

$f1 = $doc->addFont('Helvetica');
$f2 = $doc->addFont('Helvetica-Bold');

$page = $doc->addPage();

$page->add(
    Text::write('Hello, World!')
        ->at(72, 720)->font($f2, 24)->color(Color::black()),

    Line::from(72, 708)->to(250, 708)->color(Color::hex('#333333'))->width(0.75),

    Text::write('Generated with the Papier PHP PDF Library (ISO 32000-1)')
        ->at(72, 690)->font($f1, 12)->rgb(0.3, 0.3, 0.3),
);

$doc->save(__DIR__ . '/output/01_hello_world.pdf');
echo "Created: 01_hello_world.pdf\n";
