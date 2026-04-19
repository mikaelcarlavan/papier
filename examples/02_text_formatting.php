<?php

/**
 * Example 02: Text formatting
 *
 * Demonstrates fonts, sizes, colours, character/word spacing, leading,
 * text rendering modes, and multi-line paragraphs.
 *
 * Basic text uses the Text / TextBox elements.  Advanced typography
 * features (char spacing, rendering modes) that have no element
 * equivalent are rendered directly via ContentStream.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Color, Line, Text, TextBox};

$doc = PdfDocument::create();
$doc->setTitle('Text Formatting');

$regular   = $doc->addFont('Times-Roman');
$bold      = $doc->addFont('Times-Bold');
$italic    = $doc->addFont('Times-Italic');
$mono      = $doc->addFont('Courier');
$helvetica = $doc->addFont('Helvetica');

$page = $doc->addPage();

// ── Title ─────────────────────────────────────────────────────────────────────
$page->add(
    Text::write('Text Formatting Showcase')
        ->at(72, 780)->font($bold, 28)->color(Color::hex('#000066')),
    Line::from(72, 772)->to(523, 772)->color(Color::hex('#000066'))->width(1.5),
);

// ── Font size variations ──────────────────────────────────────────────────────
$y = 750;
foreach ([8, 10, 12, 14, 18, 24, 36] as $size) {
    $page->add(
        Text::write("{$size}pt Times-Roman — The quick brown fox jumps")
            ->at(72, $y)->font($regular, $size)
    );
    $y -= $size + 4;
}

// ── Font family ───────────────────────────────────────────────────────────────
$y -= 10;
$pangram = 'The quick brown fox jumps over the lazy dog.';
foreach ([
    [$regular,   'Times-Roman'],
    [$bold,      'Times-Bold'],
    [$italic,    'Times-Italic'],
    [$mono,      'Courier'],
    [$helvetica, 'Helvetica'],
] as [$name, $label]) {
    $page->add(
        Text::write("$label: $pangram")->at(72, $y)->font($name, 12)
    );
    $y -= 18;
}

// ── Colour spectrum ───────────────────────────────────────────────────────────
$y -= 10;
$page->add(Text::write('Colour spectrum:')->at(72, $y)->font($bold, 12));
$y -= 16;

foreach ([
    [Color::rgb(1,0,0),      'Red'],
    [Color::rgb(1,0.5,0),    'Orange'],
    [Color::rgb(0.8,0.8,0),  'Yellow'],
    [Color::rgb(0,0.6,0),    'Green'],
    [Color::rgb(0,0,1),      'Blue'],
    [Color::rgb(0.5,0,0.5),  'Purple'],
] as $i => [$color, $name]) {
    $page->add(
        Text::write($name)->at(72 + $i * 75, $y)->font($bold, 14)->color($color)
    );
}

// ── Advanced typography — ContentStream required ───────────────────────────────
// Character spacing, word spacing, horizontal scaling, and text rendering modes
// are low-level PDF graphics-state parameters with no element equivalent.
$y -= 30;
$cs = new ContentStream();

$cs->beginText()->setFont($regular, 12)->setFillRGB(0,0,0)
   ->setTextPosition(72, $y)->setCharSpacing(0)->setWordSpacing(0)
   ->showText('Normal spacing')->endText();
$y -= 16;

$cs->beginText()->setFont($regular, 12)->setFillRGB(0,0,0)
   ->setTextPosition(72, $y)->setCharSpacing(3)
   ->showText('Wide character spacing (+3)')->endText();
$y -= 16;

$cs->beginText()->setFont($regular, 12)->setFillRGB(0,0,0)
   ->setTextPosition(72, $y)->setCharSpacing(0)->setWordSpacing(10)
   ->showText('Wide word spacing (+10)')->endText();
$y -= 16;

$cs->beginText()->setFont($regular, 12)->setFillRGB(0,0,0)
   ->setTextPosition(72, $y)->setCharSpacing(0)->setWordSpacing(0)
   ->setHorizontalScaling(150)->showText('Horizontal scaling 150%')->endText();
$cs->beginText()->setHorizontalScaling(100)->endText();

$y -= 30;
$cs->setStrokeRGB(0.2, 0.2, 0.8);
foreach ([
    [0, 'Fill (mode 0)'],
    [1, 'Stroke (mode 1)'],
    [2, 'Fill+Stroke (mode 2)'],
] as [$mode, $label]) {
    $cs->beginText()->setFont($bold, 18)->setFillRGB(0.1, 0.5, 0.1)
       ->setTextRenderMode($mode)->setTextPosition(72, $y)
       ->showText($label)->endText();
    $y -= 26;
}
$cs->beginText()->setTextRenderMode(0)->endText();

$page->addContent($cs);

// ── Multi-line word-wrapped paragraph ─────────────────────────────────────────
$y -= 14;
$page->add(
    Text::write('Word-wrapped paragraph (TextBox):')->at(72, $y)->font($bold, 11)->color(Color::hex('#444444')),
);
$y -= 4;
$page->add(
    TextBox::write(
        'The TextBox element automatically wraps long text to fit a bounding '
      . 'width, using AFM font metrics for accurate line breaking. It supports '
      . 'left, center, and right alignment and a configurable line-height.'
    )
        ->at(72, $y)->size(451, 80)
        ->font($regular, 11, 'Times-Roman')
        ->lineHeight(1.5),
);

$doc->save(__DIR__ . '/output/02_text_formatting.pdf');
echo "Created: 02_text_formatting.pdf\n";
