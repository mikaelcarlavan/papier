<?php

/**
 * Example 03: Vector graphics
 *
 * Demonstrates paths, fills, strokes, line styles, dash patterns,
 * bezier curves, ellipses, transformations, and colour spaces.
 *
 * Basic shapes use Rectangle, Circle, and Line elements.
 * Features without element equivalents (Bézier curves, clipping paths,
 * CTM transformations, CMYK colour boxes) use ContentStream directly.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Circle, Color, Line, Rectangle, Text};

$doc = PdfDocument::create();
$doc->setTitle('Vector Graphics');

$f1 = $doc->addFont('Helvetica-Bold');
$page = $doc->addPage();

// ── Title ─────────────────────────────────────────────────────────────────────
$page->add(
    Text::write('Vector Graphics Showcase')->at(72, 800)->font($f1, 20),
);

// ── 1. Basic shapes ───────────────────────────────────────────────────────────
$page->add(
    Text::write('1. Basic shapes')->at(72, 775)->font($f1, 11),

    Rectangle::create(72, 730, 100, 30)
        ->fill(Color::rgb(0.2, 0.4, 0.8))->stroke(Color::rgb(0.1, 0.2, 0.5), 1.5),

    Circle::create(230, 745, 20)
        ->fill(Color::rgb(0.8, 0.2, 0.2))->stroke(Color::rgb(0.5, 0.1, 0.1), 1.0),

    Circle::ellipse(340, 745, 50, 20)
        ->fill(Color::rgb(0.2, 0.7, 0.3))->stroke(Color::rgb(0.1, 0.4, 0.1), 1.0),
);

// Triangle (no element — raw path)
$cs = new ContentStream();
$cs->setFillRGB(0.9, 0.6, 0.1)->setStrokeRGB(0.5, 0.3, 0.0)
   ->moveTo(430, 730)->lineTo(480, 730)->lineTo(455, 770)->closePath()->fillStroke();
$page->addContent($cs);

// ── 2. Line styles ────────────────────────────────────────────────────────────
$page->add(
    Text::write('2. Line styles (cap, join, dash)')->at(72, 715)->font($f1, 11),
);

// Line cap / join styles need raw ContentStream (no element parameter)
$cs2 = new ContentStream();
$y = 700;
foreach ([
    [0, 'Butt cap'],
    [1, 'Round cap'],
    [2, 'Projecting square cap'],
] as [$cap, $label]) {
    $cs2->save()->setStrokeRGB(0,0,0)->setLineWidth(8)->setLineCap($cap)
        ->moveTo(72, $y)->lineTo(200, $y)->stroke()->restore();
    $y -= 18;
}
$page->addContent($cs2);

$y = 700; // reset for labels
$page->add(
    Text::write('Butt cap')               ->at(205, $y - 3) ->font($f1, 9),
    Text::write('Round cap')              ->at(205, $y - 21)->font($f1, 9),
    Text::write('Projecting square cap')  ->at(205, $y - 39)->font($f1, 9),

    Line::from(72, 646)->to(200, 646)->color(Color::hex('#3333cc'))->width(2)->dash([10, 5]),
    Text::write('Dash [10,5]')->at(205, 642)->font($f1, 9),

    Line::from(72, 628)->to(200, 628)->color(Color::hex('#cc3333'))->width(2)->dash([3, 3, 8, 3]),
    Text::write('Dash [3,3,8,3]')->at(205, 624)->font($f1, 9),
);

// Miter / round join (needs setLineJoin — raw CS)
$cs3 = new ContentStream();
$x = 300; $y = 700;
$cs3->save()->setStrokeRGB(0,0,0)->setLineWidth(8)->setLineJoin(0)->setMiterLimit(4)
    ->moveTo($x, $y)->lineTo($x+40, $y-30)->lineTo($x+80, $y)->stroke()->restore();
$x = 420;
$cs3->save()->setStrokeRGB(0,0,0)->setLineWidth(8)->setLineJoin(1)
    ->moveTo($x, $y)->lineTo($x+40, $y-30)->lineTo($x+80, $y)->stroke()->restore();
$page->addContent($cs3);
$page->add(
    Text::write('Miter join')->at(300, 705)->font($f1, 9),
    Text::write('Round join')->at(420, 705)->font($f1, 9),
);

// ── 3. Bézier curves — ContentStream only ────────────────────────────────────
$page->add(Text::write('3. Cubic Bézier curves')->at(72, 620)->font($f1, 11));

$cs4 = new ContentStream();
$cs4->setStrokeRGB(0.6, 0.0, 0.6)->setLineWidth(2)->setDash([])
    ->moveTo(72, 600)->curveTo(120, 640, 170, 560, 220, 600)->stroke();
$cs4->setStrokeRGB(0.0, 0.6, 0.6)->setLineWidth(2)
    ->moveTo(250, 580)->curveTo(300, 640, 340, 540, 390, 580)->stroke();
// Control point markers
$cs4->setFillRGB(1,0,0)->drawCircle(120, 640, 3)->fill();
$cs4->setFillRGB(1,0,0)->drawCircle(170, 560, 3)->fill();
$page->addContent($cs4);

// ── 4. CTM transformations — ContentStream only ───────────────────────────────
$page->add(Text::write('4. CTM transformations')->at(72, 530)->font($f1, 11));

$cs5 = new ContentStream();
$starX = 140; $starY = 460;
for ($i = 0; $i < 8; $i++) {
    $cs5->save()->translate($starX, $starY)->rotate($i * 45)
        ->setStrokeRGB($i/8, 1-$i/8, 0.5)->setLineWidth(2)
        ->moveTo(-40, 0)->lineTo(40, 0)->stroke()->restore();
}
$cs5->save()->translate(290, 430)->scale(1.5, 0.8)
    ->setFillRGB(0.9, 0.8, 0.0)->setStrokeRGB(0.6, 0.5, 0.0)->setLineWidth(1.5)
    ->drawRect(0, 0, 60, 60, true, true)->restore();
$page->addContent($cs5);

$page->add(
    Text::write('8 rotated lines (45° each)')->at(100, 420)->font($f1, 9),
    Text::write('Scaled (1.5×0.8)')          ->at(280, 420)->font($f1, 9),
);

// ── 5. CMYK colours ───────────────────────────────────────────────────────────
$page->add(Text::write('5. CMYK colour model')->at(72, 390)->font($f1, 11));

$cmykColors = [
    [Color::cmyk(1,0,0,0), 'Cyan'],
    [Color::cmyk(0,1,0,0), 'Magenta'],
    [Color::cmyk(0,0,1,0), 'Yellow'],
    [Color::cmyk(0,0,0,1), 'Black'],
    [Color::cmyk(0.5,0,0.5,0), 'Green-ish'],
    [Color::cmyk(0,0.5,0.5,0), 'Red-ish'],
];
$x = 72;
foreach ($cmykColors as [$color, $label]) {
    $page->add(
        Rectangle::create($x, 355, 70, 25)->fill($color),
        Text::write($label)->at($x + 5, 360)->font($f1, 8),
    );
    $x += 75;
}

// ── 6. Clipping paths — ContentStream only ────────────────────────────────────
$page->add(Text::write('6. Clipping paths')->at(72, 330)->font($f1, 11));

$cs6 = new ContentStream();
$cs6->save()
    ->drawCircle(130, 290, 35)->clip()->endPath()
    ->setFillRGB(1,0,0)->drawRect(95, 255, 70, 70, true, false)
    ->setFillRGB(0,0,1)->drawRect(95, 255, 35, 70, true, false)
    ->restore();
$page->addContent($cs6);
$page->add(Text::write('Circular clip')->at(100, 248)->font($f1, 9));

$doc->save(__DIR__ . '/output/03_graphics.pdf');
echo "Created: 03_graphics.pdf\n";
