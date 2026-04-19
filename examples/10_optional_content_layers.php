<?php

/**
 * Example 10: Optional Content (Layers) — §8.11
 *
 * Creates a PDF with three optional content groups (layers):
 * "Background", "Text Content", and "Annotations".
 * Each layer can be toggled on/off in Acrobat/compatible viewers.
 *
 * Layer content (Background, Text Content, Annotations) must be wrapped in
 * marked-content operators (BDC/EMC) for layer toggling to work, which
 * requires ContentStream directly. The page title outside any layer uses
 * the elements API.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\{Color, Text};
use Papier\OptionalContent\{OCGroup, OCMembership, OCProperties};
use Papier\Objects\{PdfName, PdfString};

$doc  = PdfDocument::create();
$doc->setTitle('Optional Content Layers');
$font = $doc->addFont('Helvetica',      'F1');
$bold = $doc->addFont('Helvetica-Bold', 'F2');

$page = $doc->addPage();

// ── Define OCGs ───────────────────────────────────────────────────────────────
$bgGroup    = new OCGroup('Background');
$textGroup  = new OCGroup('Text Content');
$annotGroup = new OCGroup('Annotations');

$bgDict    = $bgGroup->getDictionary();
$textDict  = $textGroup->getDictionary();
$annotDict = $annotGroup->getDictionary();

$page->getResources()->addProperties('OCBackground',   $bgDict);
$page->getResources()->addProperties('OCText',         $textDict);
$page->getResources()->addProperties('OCAnnotations',  $annotDict);

// ── Layer 1: Background ───────────────────────────────────────────────────────
// Marked-content operators are required for layer toggling — ContentStream only
$cs = new ContentStream();
$cs->beginMarkedContentProps('OC', 'OCBackground');
$cs->setFillRGB(0.95, 0.95, 1.0)
   ->drawRect(0, 0, 595, 841, true, false);
$cs->setStrokeRGB(0.5, 0.5, 0.8)->setLineWidth(3)
   ->drawRect(30, 30, 535, 781, false, true);
$cs->endMarkedContent();

// ── Layer 2: Text Content ─────────────────────────────────────────────────────
$cs->beginMarkedContentProps('OC', 'OCText');
$cs->beginText()
       ->setFont('F2', 26)->setFillRGB(0.2, 0.2, 0.6)
       ->setTextPosition(72, 760)
       ->showText('Optional Content (Layers) Demo')
   ->endText();

$cs->beginText()
       ->setFont('F1', 13)->setFillRGB(0, 0, 0)
       ->setTextPosition(72, 720)
       ->showText('This text is on the "Text Content" layer.')
   ->endText();

$paragraphs = [
    'Toggle layers on/off using the Layers panel in your PDF viewer.',
    'Layer 1 (Background): Coloured background and decorative border.',
    'Layer 2 (Text Content): This paragraph and the heading above.',
    'Layer 3 (Annotations): Red circles and yellow highlighted regions.',
    '',
    'Optional content groups (OCGs) are defined in §8.11 of ISO 32000-1.',
    'The OCProperties entry in the document catalog lists all groups.',
];
$y = 690;
foreach ($paragraphs as $para) {
    $cs->beginText()
           ->setFont('F1', 11)->setFillRGB(0.1, 0.1, 0.1)
           ->setTextPosition(90, $y)
           ->showText($para)
       ->endText();
    $y -= 18;
}
$cs->endMarkedContent();

// ── Layer 3: Annotations (visual markup) ─────────────────────────────────────
$cs->beginMarkedContentProps('OC', 'OCAnnotations');
$cs->setStrokeRGB(1, 0, 0)->setLineWidth(2);
$cs->drawCircle(100, 500, 30)->stroke();
$cs->drawCircle(200, 500, 30)->stroke();
$cs->drawCircle(300, 500, 30)->stroke();

$cs->setFillRGB(1, 1, 0);
for ($i = 0; $i < 5; $i++) {
    $cs->drawRect(72 + $i * 90, 450, 75, 20, true, false);
}
$cs->beginText()
       ->setFont('F2', 10)->setFillRGB(0,0,0)
       ->setTextPosition(72, 440)
       ->showText('Annotation layer: circles and highlight boxes visible above')
   ->endText();
$cs->endMarkedContent();

$page->addContent($cs);

// ── Set up OCProperties in the document catalog ───────────────────────────────
$ocProps = new OCProperties();
$ocProps->addOCG($bgGroup)
        ->addOCG($textGroup)
        ->addOCG($annotGroup)
        ->setDefaultConfig(
            name:      'Default',
            on:        [$bgGroup, $textGroup, $annotGroup],
            baseState: 'ON',
        );

$doc->setOCProperties($ocProps);
$doc->save(__DIR__ . '/output/10_optional_content.pdf');
echo "Created: 10_optional_content.pdf\n";
