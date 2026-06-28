<?php

/**
 * Example 27: Shading patterns (Pattern type 2)
 *
 * A shading pattern lets you fill ANY shape — or text — with a gradient
 * (axial, radial, or a mesh), via the Pattern colour space, rather than only
 * painting a clipped region with the `sh` operator.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Color, Text};
use Papier\Graphics\Pattern\ShadingPattern;
use Papier\Graphics\Shading\{AxialShading, RadialShading};
use Papier\Function\ExponentialFunction;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$doc  = PdfDocument::create();
$doc->setTitle('Shading patterns');
$font    = $doc->addFont('Helvetica-Bold');
$bigFont = $doc->addFont('Helvetica-Bold', 'FBig'); // used inside a raw content stream
$page    = $doc->addPage();

$page->add(Text::write('Shading patterns (fill shapes & text)')->at(72, 790)->font($font, 16)->color(Color::hex('#1a1a2e')));

// A red→blue gradient function shared by both shadings.
$grad = new ExponentialFunction(
    domain: [0.0, 1.0],
    range:  [0.0, 1.0, 0.0, 1.0, 0.0, 1.0],
    n:      1.0,
    c0:     [0.9, 0.1, 0.1],
    c1:     [0.1, 0.2, 0.9],
);

// 1. Rectangle filled with an axial (linear) shading pattern.
$axial = new AxialShading('DeviceRGB', 72, 0, 372, 0);
$axial->setFunction($grad->toPdfObject())->setExtend(true, true);
$page->getResources()->addPattern('GradH', (new ShadingPattern($axial))->getDictionary());

$cs = new ContentStream();
$cs->save()
   ->setFillColorSpace('Pattern')->setFillColorN('GradH')
   ->drawRect(72, 690, 300, 60)->fill()
   ->restore();
$page->addContent($cs);
$page->add(Text::write('Axial pattern on a rectangle')->at(72, 676)->font($font, 10));

// 2. Large text filled with a radial shading pattern.
$radial = new RadialShading('DeviceRGB', 250, 580, 0, 250, 580, 180);
$radial->setFunction($grad->toPdfObject())->setExtend(true, true);
$page->getResources()->addPattern('GradR', (new ShadingPattern($radial))->getDictionary());

$cs2 = new ContentStream();
$cs2->save()
    ->setFillColorSpace('Pattern')->setFillColorN('GradR')
    ->beginText()
    ->setFont('FBig', 72)
    ->setTextPosition(72, 560)
    ->showText('PAPIER')
    ->endText()
    ->restore();
$page->addContent($cs2);
$page->add(Text::write('Radial pattern filling text glyphs')->at(72, 540)->font($font, 10));

$file = "$outDir/27_shading_patterns.pdf";
$doc->save($file);
echo "Created: 27_shading_patterns.pdf (" . number_format(filesize($file)) . " bytes)\n";
$pdf = file_get_contents($file);
echo "Has Pattern type 2: " . (str_contains($pdf, '/PatternType 2') ? 'yes' : 'no') . "\n";

echo "\nDone.\n";
