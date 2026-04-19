<?php

/**
 * Example 15: Embedding TTF / OTF fonts
 *
 * Demonstrates loading a TrueType font file via the standard addFont() API:
 *   - Pass a .ttf or .otf file path to $doc->addFont() — no extra steps needed.
 *   - The PostScript name, glyph widths, and font program are embedded automatically.
 *   - Works alongside the built-in standard Type 1 fonts in the same document.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Line, Rectangle, Text};

$doc = PdfDocument::create();
$doc->setTitle('TTF / OTF Font Embedding');

// ── Fonts ─────────────────────────────────────────────────────────────────────
// Standard built-in Type 1 font — no file needed
$helvetica     = $doc->addFont('Helvetica');
$helveticaBold = $doc->addFont('Helvetica-Bold');

// TTF loaded from a file path — metrics and font program embedded automatically
$lato = $doc->addFont(__DIR__ . '/Lato-Regular.ttf');

// ── Page ──────────────────────────────────────────────────────────────────────
$page = $doc->addPage();

$page->add(
    Rectangle::create(0, 800, 595, 42)->fill(Color::rgb(0.08, 0.15, 0.35)),
    Text::write('TTF / OTF Font Embedding')->at(36, 815)
        ->font($helveticaBold, 16)->color(Color::white()),
);

// ── Section: embedded TTF ─────────────────────────────────────────────────────
$page->add(
    Text::write('Embedded TTF: Lato Regular')->at(36, 770)
        ->font($helveticaBold, 11)->color(Color::rgb(0.08, 0.15, 0.35)),
    Line::from(36, 764)->to(559, 764)->color(Color::rgb(0.08, 0.15, 0.35))->width(0.5),
);

$sizes = [8, 10, 12, 14, 18, 24];
$y = 748;
foreach ($sizes as $size) {
    $page->add(
        Text::write("{$size}pt — The quick brown fox jumps over the lazy dog.")
            ->at(36, $y)->font($lato, $size)->color(Color::black()),
    );
    $y -= $size + 6;
}

// ── Section: side-by-side comparison ─────────────────────────────────────────
$page->add(
    Text::write('Side-by-side comparison at 12 pt')->at(36, $y - 14)
        ->font($helveticaBold, 11)->color(Color::rgb(0.08, 0.15, 0.35)),
    Line::from(36, $y - 20)->to(559, $y - 20)
        ->color(Color::rgb(0.08, 0.15, 0.35))->width(0.5),
);

$pairs = [
    [$lato,      'Lato Regular (TTF)'],
    [$helvetica, 'Helvetica (Type 1 built-in)'],
];
$cy = $y - 38;
foreach ($pairs as [$font, $label]) {
    $page->add(
        Text::write($label . ':')->at(36, $cy)
            ->font($helveticaBold, 10)->color(Color::gray(0.3)),
        Text::write('AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz')
            ->at(36, $cy - 14)->font($font, 12)->color(Color::black()),
        Text::write('0123456789  !?.,;:  ""\'\'  —–  @#%&')
            ->at(36, $cy - 30)->font($font, 12)->color(Color::black()),
    );
    $cy -= 56;
}

// ── Section: note ─────────────────────────────────────────────────────────────
$page->add(
    Rectangle::create(36, $cy - 52, 523, 48)->fill(Color::rgb(0.94, 0.96, 1.0)),
    Text::write('How it works:')->at(46, $cy - 8)
        ->font($helveticaBold, 10)->color(Color::rgb(0.08, 0.15, 0.35)),
    Text::write('Pass a .ttf or .otf file path to $doc->addFont() — the same method used for')
        ->at(46, $cy - 22)->font($helvetica, 9)->color(Color::gray(0.25)),
    Text::write('built-in fonts. Glyph metrics, the PostScript name, and the full font program')
        ->at(46, $cy - 34)->font($helvetica, 9)->color(Color::gray(0.25)),
    Text::write('are embedded automatically. No extra classes or configuration required.')
        ->at(46, $cy - 46)->font($helvetica, 9)->color(Color::gray(0.25)),
);

// ─────────────────────────────────────────────────────────────────────────────
$doc->save(__DIR__ . '/output/15_ttf_fonts.pdf');
echo "Created: 15_ttf_fonts.pdf\n";
echo "  Lato Regular TTF embedded via \$doc->addFont(__DIR__ . '/Lato-Regular.ttf')\n";
