<?php

/**
 * Example 06: Annotations
 *
 * Demonstrates text notes, link annotations, highlight/underline/strikeout,
 * geometric markup (lines, squares, circles, polygons), stamps, and ink.
 *
 * Background text and page decoration use the elements API.
 * Annotation objects (TextAnnotation, LinkAnnotation, etc.) are registered
 * through the annotations API — no element equivalent exists for those.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\{Color, Rectangle, Text, TextBox};
use Papier\Annotation\{
    TextAnnotation, LinkAnnotation, FreeTextAnnotation,
    HighlightAnnotation, UnderlineAnnotation, StrikeOutAnnotation,
    SquareAnnotation, CircleAnnotation, LineAnnotation,
    PolygonAnnotation, StampAnnotation, InkAnnotation,
    PopupAnnotation, SquigglyAnnotation
};

$doc  = PdfDocument::create();
$doc->setTitle('Annotations Showcase');
$font = $doc->addFont('Helvetica', 'F1');
$bold = $doc->addFont('Helvetica-Bold', 'F2');
$page = $doc->addPage();

// ── Background text that annotations will mark up ─────────────────────────────
$text = 'This paragraph contains sample text that will be marked up with various '
      . 'PDF annotation types including highlights, underlines, strikeouts, '
      . 'and squiggly underlines as defined in ISO 32000-1 §12.5.6.';

$page->add(
    Text::write('Annotations Showcase')->at(72, 790)->font($bold, 18)->color(Color::black()),
    TextBox::write($text)
        ->at(72, 760)->size(430, 55)
        ->font($font, 11, 'Helvetica')
        ->lineHeight(1.45),
);

// ── 1. Text (sticky note) annotation ─────────────────────────────────────────
$textAnnot = new TextAnnotation(500, 745, 520, 765);
$textAnnot->setContents('This is a sticky-note annotation (Text subtype).')
          ->setIcon('Note')
          ->setColor(1.0, 0.9, 0.0)  // yellow
          ->setFlags(4); // Print flag
$page->addAnnotation($textAnnot);

// ── 2. Highlight annotation ───────────────────────────────────────────────────
$highlight = new HighlightAnnotation(72, 756, 400, 770);
$highlight->setQuadPoints([72,770, 400,770, 72,756, 400,756])
          ->setColor(1.0, 1.0, 0.0)  // yellow
          ->setContents('Highlighted section');
$page->addAnnotation($highlight);

// ── 3. Underline annotation ───────────────────────────────────────────────────
$underline = new UnderlineAnnotation(72, 740, 300, 754);
$underline->setQuadPoints([72,754, 300,754, 72,740, 300,740])
          ->setColor(0.0, 0.0, 1.0)  // blue
          ->setContents('Underlined text');
$page->addAnnotation($underline);

// ── 4. StrikeOut annotation ───────────────────────────────────────────────────
$strikeOut = new StrikeOutAnnotation(72, 724, 200, 738);
$strikeOut->setQuadPoints([72,738, 200,738, 72,724, 200,724])
          ->setColor(1.0, 0.0, 0.0)  // red
          ->setContents('Struck out text');
$page->addAnnotation($strikeOut);

// ── 5. Squiggly annotation ────────────────────────────────────────────────────
$squiggly = new SquigglyAnnotation(200, 724, 400, 738);
$squiggly->setQuadPoints([200,738, 400,738, 200,724, 400,724])
         ->setColor(1.0, 0.5, 0.0)  // orange
         ->setContents('Spelling error?');
$page->addAnnotation($squiggly);

// ── 6. Link annotation ────────────────────────────────────────────────────────
$page->add(
    Rectangle::create(72, 680, 200, 20)->fill(Color::rgb(0.2, 0.4, 0.8)),
    Text::write('Click to visit example.com')->at(80, 686)->font($font, 11)->color(Color::white()),
);

$link = new LinkAnnotation(72, 680, 272, 700);
$link->setURI('https://example.com')
     ->setHighlightMode('P')  // push
     ->setBorderStyle(0);     // no border
$page->addAnnotation($link);

// ── 7. Free text annotation ───────────────────────────────────────────────────
$freeText = new FreeTextAnnotation(72, 640, 300, 670);
$freeText->setContents('This is a FreeText annotation — text rendered directly on the page.')
         ->setDefaultAppearance('F1', 10)
         ->setColor(Color::rgb(0.9, 0.95, 1.0));  // light blue background
$page->addAnnotation($freeText);

// ── 8. Line annotation ────────────────────────────────────────────────────────
$line = new LineAnnotation(72, 600, 300, 630, 72, 600, 300, 620);
$line->setContents('A line annotation with arrows')
     ->setColor(0.8, 0.0, 0.0)
     ->setLineEndings('OpenArrow', 'ClosedArrow')
     ->setInteriorColor(1.0, 0.8, 0.0)
     ->setBorderStyle(2.0)
     ->finalize();
$page->addAnnotation($line);

// ── 9. Square annotation ──────────────────────────────────────────────────────
$square = new SquareAnnotation(320, 600, 500, 630);
$square->setContents('A square annotation')
       ->setColor(0.0, 0.5, 0.0)
       ->setInteriorColor(0.9, 1.0, 0.9)
       ->setBorderStyle(1.5);
$page->addAnnotation($square);

// ── 10. Circle annotation ─────────────────────────────────────────────────────
$circle = new CircleAnnotation(72, 560, 200, 600);
$circle->setContents('A circle annotation')
       ->setColor(0.0, 0.0, 0.8)
       ->setInteriorColor(0.9, 0.9, 1.0)
       ->setBorderStyle(1.5);
$page->addAnnotation($circle);

// ── 11. Polygon annotation ────────────────────────────────────────────────────
$polygon = new PolygonAnnotation(220, 555, 380, 605);
$polygon->setVertices([220,555, 300,605, 380,555, 340,575, 260,575])
        ->setContents('A polygon annotation')
        ->setColor(0.5, 0.0, 0.5);
$page->addAnnotation($polygon);

// ── 12. Stamp annotation ──────────────────────────────────────────────────────
$stamp = new StampAnnotation(390, 555, 520, 605);
$stamp->setIcon('Draft')
      ->setContents('DRAFT')
      ->setColor(1.0, 0.0, 0.0);
$page->addAnnotation($stamp);

// ── 13. Ink annotation ────────────────────────────────────────────────────────
$ink = new InkAnnotation(72, 500, 300, 545);
$ink->setInkList([
    [72,520, 100,540, 140,510, 180,535, 220,515, 260,530, 300,520],
])
    ->setColor(0.0, 0.6, 0.0)
    ->setBorderStyle(2.0)
    ->setContents('A freehand ink annotation');
$page->addAnnotation($ink);

$page->add(
    Text::write('13 annotation types demonstrated above.')->at(72, 480)->font($bold, 12),
);

$doc->save(__DIR__ . '/output/06_annotations.pdf');
echo "Created: 06_annotations.pdf\n";
