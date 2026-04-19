<?php

/**
 * Example 08: Advanced graphics — shadings, transparency, patterns
 *
 * Demonstrates axial/radial gradients (shading patterns), transparency
 * groups with blend modes, extended graphics states (opacity), and
 * tiling patterns.
 *
 * Section headings use the elements API.
 * Shadings, ExtGState, and tiling patterns require ContentStream — no
 * element equivalent exists for those PDF graphics features.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Color, Text};
use Papier\Function\ExponentialFunction;
use Papier\Graphics\Shading\{AxialShading, RadialShading};
use Papier\Graphics\Pattern\TilingPattern;
use Papier\Graphics\Transparency\ExtGState;
use Papier\Objects\{PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfName, PdfReal, PdfStream};

$doc  = PdfDocument::create();
$doc->setTitle('Advanced Graphics');
$font = $doc->addFont('Helvetica',      'F1');
$bold = $doc->addFont('Helvetica-Bold', 'F2');
$page = $doc->addPage();

$page->add(
    Text::write('Advanced Graphics Showcase')->at(72, 800)->font($bold, 20),
);

// ── 1. Axial shading (linear gradient) ───────────────────────────────────────
$page->add(
    Text::write('1. Axial Gradient (DeviceRGB)')->at(72, 775)->font($bold, 12),
);

$cs = new ContentStream();

// Build exponential function: C0=[1,0,0] (red) → C1=[0,0,1] (blue)
$gradFn = new ExponentialFunction(
    domain: [0.0, 1.0],
    range:  [0.0, 1.0, 0.0, 1.0, 0.0, 1.0],
    n:      1.0,
    c0:     [1.0, 0.0, 0.0],
    c1:     [0.0, 0.0, 1.0],
);
$gradFnObj = $gradFn->toPdfObject();

$axial = new AxialShading('DeviceRGB', 72, 0, 523, 0);
$axial->setFunction($gradFnObj)->setExtend(true, true);
$axialDict = $axial->toDictionary();

$page->getResources()->addShading('Sh1', $axialDict);
$cs->save()
   ->drawRect(72, 720, 451, 40)
   ->clip()->endPath()
   ->shading('Sh1')
   ->restore();

// ── 2. Radial shading ────────────────────────────────────────────────────────
$page->addContent($cs);
$page->add(
    Text::write('2. Radial Gradient (centre glow)')->at(72, 710)->font($bold, 12),
);
$cs = new ContentStream();

$radFn = new ExponentialFunction(
    domain: [0.0, 1.0],
    range:  [0.0, 1.0, 0.0, 1.0, 0.0, 1.0],
    n:      1.0,
    c0:     [1.0, 1.0, 0.0],   // yellow centre
    c1:     [0.0, 0.0, 0.3],   // dark blue edge
);
$radFnObj = $radFn->toPdfObject();

$radial = new RadialShading('DeviceRGB', 297, 660, 0, 297, 660, 80);
$radial->setFunction($radFnObj)->setExtend(true, true);
$radialDict = $radial->toDictionary();

$page->getResources()->addShading('Sh2', $radialDict);
$cs->save()
   ->drawCircle(297, 660, 80)
   ->clip()->endPath()
   ->shading('Sh2')
   ->restore();

// ── 3. Multi-stop gradient using stitching function ───────────────────────────
$page->addContent($cs);
$page->add(
    Text::write('3. Multi-stop Gradient (stitching function)')->at(72, 565)->font($bold, 12),
);
$cs = new ContentStream();

$stitch = new \Papier\Function\StitchingFunction(
    domain:    [0, 1],
    range:     [0,1, 0,1, 0,1],
    functions: [new \Papier\Function\ExponentialFunction([0,1],[0,1,0,1,0,1],1.0,[1,0,0],[0,1,0]),
                new \Papier\Function\ExponentialFunction([0,1],[0,1,0,1,0,1],1.0,[0,1,0],[0,0,1])],
    bounds:    [0.5],
    encode:    [0,1, 0,1],
);

$stitchObj = $stitch->toPdfObject();
$axial2 = new AxialShading('DeviceRGB', 72, 0, 523, 0);
$axial2->setFunction($stitchObj)->setExtend(true, true);
$page->getResources()->addShading('Sh3', $axial2->toDictionary());
$cs->save()
   ->drawRect(72, 530, 451, 30)
   ->clip()->endPath()
   ->shading('Sh3')
   ->restore();

// ── 4. Extended graphics states — transparency / opacity ─────────────────────
$page->addContent($cs);
$page->add(
    Text::write('4. Transparency / opacity (ExtGState)')->at(72, 510)->font($bold, 12),
);
$cs = new ContentStream();

$opacities = [[1.0,0.0,0.0, 0.5], [0.0,1.0,0.0, 0.5], [0.0,0.0,1.0, 0.5]];
$centers   = [[180, 460], [240, 460], [210, 490]];
$gsNames   = ['GS1', 'GS2', 'GS3'];

foreach (array_keys($opacities) as $i) {
    $gs = new ExtGState();
    $gs->setFillAlpha($opacities[$i][3])->setBlendMode('Multiply');
    $page->getResources()->addExtGState($gsNames[$i], $gs->getDictionary());
}

foreach (array_keys($opacities) as $i) {
    [$r, $g, $b, $a] = $opacities[$i];
    [$cx, $cy]       = $centers[$i];
    $cs->save()
       ->setExtGState($gsNames[$i])
       ->setFillRGB($r, $g, $b)
       ->drawCircle($cx, $cy, 35)
       ->fill()
       ->restore();
}

$page->addContent($cs);
$page->add(
    Text::write('Blend mode: Multiply, opacity: 50%')->at(150, 418)->font($font, 9)->rgb(0.4, 0.4, 0.4),
);

// ── 5. Tiling pattern ─────────────────────────────────────────────────────────
$page->add(
    Text::write('5. Tiling pattern')->at(310, 510)->font($bold, 12),
);

$cs2 = new ContentStream();
$tile = new TilingPattern(20, 20, 0, 0, 20, 20);
$tileContent = $tile->getContent();
$tileContent
    ->setFillRGB(0.8, 0.9, 1.0)->drawRect(0, 0, 20, 20, true, false)
    ->setFillRGB(0.3, 0.5, 0.8)->drawCircle(10, 10, 6)->fill()
    ->setStrokeRGB(0.1, 0.3, 0.6)->setLineWidth(0.5)->drawCircle(10, 10, 6)->stroke();

$tileStream = $tile->getStream();
$page->getResources()->addPattern('P1', $tileStream);

$cs2->save()
    ->setFillColorSpace('Pattern')
    ->setFillColorN('P1')
    ->drawRect(310, 420, 200, 80)
    ->fill()
    ->restore();

$page->addContent($cs2);

$doc->save(__DIR__ . '/output/08_advanced_graphics.pdf');
echo "Created: 08_advanced_graphics.pdf\n";
