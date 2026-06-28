<?php

/**
 * Example 22: Mesh shadings (ISO 32000-1 §8.7.4.5 — types 4, 5, 6, 7)
 *
 * Demonstrates the four mesh shading types painted with the `sh` operator:
 *   - Type 4: free-form Gouraud triangle mesh
 *   - Type 5: lattice-form Gouraud triangle mesh
 *   - Type 6: Coons patch mesh
 *   - Type 7: tensor-product patch mesh
 *
 * Each is clipped to a rectangle so it fills a tidy swatch.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Color, Text};
use Papier\Graphics\Shading\{
    GouraudTriangleShading, LatticeTriangleShading, CoonsPatchShading, TensorPatchShading
};

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

$doc  = PdfDocument::create();
$doc->setTitle('Mesh shadings');
$font = $doc->addFont('Helvetica-Bold');
$page = $doc->addPage();

$page->add(Text::write('Mesh shadings (types 4-7)')->at(72, 790)->font($font, 18)->color(Color::hex('#1a1a2e')));

/** Paint a shading clipped to the given rectangle, with a caption. */
function swatch(PdfDocument $doc, $page, string $name, $shadingStreamOrDict, float $x, float $y, float $w, float $h, string $label, string $font): void
{
    $page->getResources()->addShading($name, $shadingStreamOrDict);
    $cs = new ContentStream();
    $cs->save()
       ->rectangle($x, $y, $w, $h)->clip()->endPath()
       ->shading($name)
       ->restore();
    $page->addContent($cs);
    $page->add(Text::write($label)->at($x, $y - 14)->font($font, 10));
}

$boldName = $font;

// Type 4 — free-form Gouraud triangles.
$t4 = new GouraudTriangleShading('DeviceRGB');
$t4->addTriangle([72, 600], [1, 0, 0], [222, 600], [0, 1, 0], [147, 740], [0, 0, 1]);
swatch($doc, $page, 'M4', $t4->toStream(), 72, 600, 150, 140, 'Type 4: Gouraud (free-form)', $boldName);

// Type 5 — lattice Gouraud (2x2 grid).
$t5 = new LatticeTriangleShading('DeviceRGB', 2);
$t5->addVertex(300, 600, [1, 0, 0])->addVertex(450, 600, [0, 1, 0])
   ->addVertex(300, 740, [0, 0, 1])->addVertex(450, 740, [1, 1, 0]);
swatch($doc, $page, 'M5', $t5->toStream(), 300, 600, 150, 140, 'Type 5: Gouraud (lattice)', $boldName);

// Type 6 — Coons patch (a single bilinear-ish patch).
$pts6 = [
    [72, 380], [102, 430], [152, 430], [222, 380],   // bottom edge (4)
    [222, 420], [222, 470],                          // right edge (2)
    [222, 520], [152, 470], [102, 470], [72, 520],   // top edge (4)
    [72, 470], [72, 420],                            // left edge (2)
];
$col6 = [[1, 0, 0], [0, 1, 0], [0, 0, 1], [1, 1, 0]];
$t6 = (new CoonsPatchShading('DeviceRGB'))->addPatch($pts6, $col6);
swatch($doc, $page, 'M6', $t6->toStream(), 72, 380, 150, 140, 'Type 6: Coons patch', $boldName);

// Type 7 — tensor patch (12 boundary + 4 interior control points).
$pts7 = $pts6;
$pts7[] = [120, 430]; $pts7[] = [180, 430]; $pts7[] = [180, 470]; $pts7[] = [120, 470];
foreach ($pts7 as $i => $p) { $pts7[$i] = [$p[0] + 228, $p[1]]; }
$t7 = (new TensorPatchShading('DeviceRGB'))->addPatch($pts7, $col6);
swatch($doc, $page, 'M7', $t7->toStream(), 300, 380, 150, 140, 'Type 7: Tensor patch', $boldName);

$file = "$outDir/22_mesh_shadings.pdf";
$doc->save($file);
echo "Created: 22_mesh_shadings.pdf (" . number_format(filesize($file)) . " bytes)\n";
echo "Done.\n";
