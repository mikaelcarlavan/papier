<?php

/**
 * Example 11: High-level Elements
 *
 * Demonstrates the element API — Text, TextBox, Image, Rectangle, Circle,
 * and Line — added directly to a page via PdfPage::add().
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Text, TextBox, Image, Rectangle, Circle, Line};

if (!extension_loaded('gd')) {
    echo "SKIP: example 11 requires the GD extension.\n";
    exit(0);
}

$doc  = PdfDocument::create();
$doc->setTitle('Elements API');

// Register fonts once at the document level; use the returned name in elements
$f1 = $doc->addFont('Helvetica');
$f2 = $doc->addFont('Helvetica-Bold');

$page = $doc->addPage();

// ── Header bar ────────────────────────────────────────────────────────────────
$page->add(
    Rectangle::create(0, 800, 595, 42)->fill(Color::hex('#2c3e7a')),
    Text::write('Papier Elements API')->at(20, 813)->font($f2, 18)->color(Color::white()),
);

// ── Section: Text ─────────────────────────────────────────────────────────────
$page->add(
    Text::write('1. Text element')->at(20, 775)->font($f2, 13)->color(Color::hex('#2c3e7a')),
    Line::from(20, 770)->to(575, 770)->color(Color::hex('#2c3e7a'))->width(0.5),

    Text::write('Default (black, 12pt)')->at(20, 752)->font($f1, 12),
    Text::write('Colored text — RGB')->at(20, 733)->font($f1, 12)->rgb(0.8, 0.2, 0.2),
    Text::write('Large bold heading')->at(20, 714)->font($f2, 20)->color(Color::hex('#1a73e8')),
    Text::write('Semi-transparent (50%)')->at(20, 692)->font($f1, 12)->opacity(0.5),
);

// ── Section: TextBox ──────────────────────────────────────────────────────────
$page->add(
    Text::write('2. TextBox — automatic word-wrap')->at(20, 668)->font($f2, 13)->color(Color::hex('#2c3e7a')),
    Line::from(20, 663)->to(575, 663)->color(Color::hex('#2c3e7a'))->width(0.5),

    Rectangle::create(20, 590, 250, 68)->fill(Color::hex('#f0f4ff'))->stroke(Color::hex('#c8d6ff'), 0.5),
    TextBox::write(
        'The quick brown fox jumps over the lazy dog. ' .
        'PDF word-wrapping using AFM metrics for the Helvetica font.'
    )
        ->at(26, 650)->size(238, 60)
        ->font($f1, 10, 'Helvetica')
        ->lineHeight(1.4),

    Rectangle::create(280, 590, 295, 68)->fill(Color::hex('#fff4f0'))->stroke(Color::hex('#ffc8b0'), 0.5),
    TextBox::write(
        'Right-aligned paragraph. Papier is a PHP library for generating ' .
        'ISO 32000-1 compliant PDF files from scratch.'
    )
        ->at(286, 650)->size(283, 60)
        ->font($f1, 10, 'Helvetica')
        ->lineHeight(1.4)
        ->align('right'),
);

// ── Section: Shapes ───────────────────────────────────────────────────────────
$page->add(
    Text::write('3. Rectangle & Circle elements')->at(20, 578)->font($f2, 13)->color(Color::hex('#2c3e7a')),
    Line::from(20, 573)->to(575, 573)->color(Color::hex('#2c3e7a'))->width(0.5),

    // Rectangles
    Rectangle::create(20,  510, 100, 55)->fill(Color::hex('#4285f4')),
    Rectangle::create(130, 510, 100, 55)->fill(Color::hex('#ea4335')),
    Rectangle::create(240, 510, 100, 55)->fill(Color::hex('#fbbc04')),
    Rectangle::create(350, 510, 100, 55)->fill(Color::hex('#34a853')),
    Rectangle::create(460, 510, 100, 55)
        ->fill(Color::white())
        ->stroke(Color::hex('#4285f4'), 2.0),

    // Circles and ellipses
    Circle::create(60,  460, 25)->fill(Color::hex('#e91e63')),
    Circle::create(140, 460, 25)->fill(Color::hex('#9c27b0'))->stroke(Color::black(), 1.0),
    Circle::ellipse(240, 460, 55, 20)->fill(Color::hex('#00bcd4')),
    Circle::create(340, 460, 25)->fill(Color::hex('#ff5722'))->opacity(0.6),
    Circle::create(420, 460, 20)->stroke(Color::hex('#607d8b'), 2.0)->noFill(),
    Circle::create(490, 460, 25)->fill(Color::hex('#8bc34a'))->stroke(Color::hex('#558b2f'), 1.5),
);

// ── Section: Lines ────────────────────────────────────────────────────────────
$page->add(
    Text::write('4. Line element')->at(20, 428)->font($f2, 13)->color(Color::hex('#2c3e7a')),
    Line::from(20, 423)->to(575, 423)->color(Color::hex('#2c3e7a'))->width(0.5),

    Line::from(20, 408)->to(200, 408)->color(Color::black())->width(1),
    Text::write('1pt solid')->at(205, 404)->font($f1, 9),

    Line::from(20, 393)->to(200, 393)->color(Color::hex('#1a73e8'))->width(3),
    Text::write('3pt blue')->at(205, 389)->font($f1, 9),

    Line::from(20, 378)->to(200, 378)->color(Color::hex('#ea4335'))->width(2)->dash([8, 4]),
    Text::write('dashed [8,4]')->at(205, 374)->font($f1, 9),

    Line::from(20, 363)->to(200, 363)->color(Color::hex('#34a853'))->width(2)->dash([2, 3]),
    Text::write('dotted [2,3]')->at(205, 359)->font($f1, 9),
);

// ── Section: Image ────────────────────────────────────────────────────────────
$page->add(
    Text::write('5. Image element')->at(20, 340)->font($f2, 13)->color(Color::hex('#2c3e7a')),
    Line::from(20, 335)->to(575, 335)->color(Color::hex('#2c3e7a'))->width(0.5),
);

// Generate test images with GD
$makeGradientJpeg = function (int $w, int $h, array $from, array $to): string {
    $img = imagecreatetruecolor($w, $h);
    for ($x = 0; $x < $w; $x++) {
        $t = $x / $w;
        $r = (int)($from[0] * (1-$t) + $to[0] * $t);
        $g = (int)($from[1] * (1-$t) + $to[1] * $t);
        $b = (int)($from[2] * (1-$t) + $to[2] * $t);
        for ($y = 0; $y < $h; $y++) {
            imagesetpixel($img, $x, $y, imagecolorallocate($img, $r, $g, $b));
        }
    }
    ob_start(); imagejpeg($img, null, 85); imagedestroy($img);
    return (string) ob_get_clean();
};

$jpeg1 = $makeGradientJpeg(200, 120, [30, 80, 200], [180, 30, 100]);
$jpeg2 = $makeGradientJpeg(200, 120, [30, 160, 80], [200, 180, 20]);

$page->add(
    Image::fromJpeg($jpeg1)->at(20,  210)->size(170, 110),
    Text::write('fromJpeg(), size(170, 110)')->at(20, 203)->font($f1, 8)->color(Color::gray(0.4)),

    Image::fromJpeg($jpeg2)->at(200, 210)->fitWidth(150),
    Text::write('fromJpeg(), fitWidth(150)')->at(200, 203)->font($f1, 8)->color(Color::gray(0.4)),

    // Same image at 50% opacity
    Image::fromJpeg($jpeg1)->at(360, 210)->size(170, 110)->opacity(0.4),
    Text::write('opacity(0.4)')->at(360, 203)->font($f1, 8)->color(Color::gray(0.4)),
);

// ── Footer ────────────────────────────────────────────────────────────────────
$page->add(
    Line::from(20, 30)->to(575, 30)->color(Color::gray(0.7))->width(0.5),
    Text::write('Papier PDF Library — Elements API demo')
        ->at(20, 18)->font($f1, 9)->color(Color::gray(0.5)),
);

$doc->save(__DIR__ . '/output/11_elements.pdf');
echo "Created: 11_elements.pdf\n";
