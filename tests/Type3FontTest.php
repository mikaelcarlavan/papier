<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\Text;
use Papier\Font\Type3Font;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName};
use Papier\Parser\PdfParser;

final class Type3FontTest extends TestCase
{
    private function makeFont(): Type3Font
    {
        $font = new Type3Font();
        $font->setFontBBox(0, 0, 750, 750);

        // 'A' (code 65): a filled square glyph.
        $a = new ContentStream();
        $a->getBuffer(); // ensure stream exists
        $glyphA = new ContentStream();
        $glyphA->rectangle(0, 0, 700, 700)->fill();
        $font->addGlyph(65, $glyphA, 750, 'square');

        // 'B' (code 66): a smaller square, no explicit name (auto-named).
        $glyphB = new ContentStream();
        $glyphB->rectangle(100, 100, 400, 400)->fill();
        $font->addGlyph(66, $glyphB, 600);

        return $font;
    }

    public function testDictionaryHasEncodingWidthsAndProcs(): void
    {
        $font = $this->makeFont();
        $dict = $font->getDictionary();

        $this->assertSame('Type3', $font->getSubtype());
        $this->assertInstanceOf(PdfInteger::class, $dict->get('FirstChar'));
        $this->assertSame(65, $dict->get('FirstChar')->getValue());
        $this->assertSame(66, $dict->get('LastChar')->getValue());

        $widths = $dict->get('Widths');
        $this->assertInstanceOf(PdfArray::class, $widths);
        $this->assertCount(2, $widths->getItems());

        $enc = $dict->get('Encoding');
        $this->assertInstanceOf(PdfDictionary::class, $enc);
        $diffs = $enc->get('Differences');
        $this->assertInstanceOf(PdfArray::class, $diffs);
        // [65 /square /g66]
        $items = $diffs->getItems();
        $this->assertSame(65, $items[0]->getValue());
        $this->assertSame('square', $items[1]->getValue());
        $this->assertSame('g66', $items[2]->getValue());

        $this->assertInstanceOf(PdfDictionary::class, $dict->get('Resources'));
    }

    public function testGlyphNameMatchesCharProcsKeys(): void
    {
        $doc  = PdfDocument::create();
        $name = $doc->registerFont($this->makeFont(), 'T3');
        $page = $doc->addPage();
        // 'AB' → codes 65, 66.
        $page->add(Text::write('AB')->at(72, 700)->font($name, 40));
        $pdf  = $doc->toString();

        // CharProcs must reference the same names the Encoding declares.
        $this->assertStringContainsString('/CharProcs', $pdf);
        $this->assertStringContainsString('/square', $pdf);
        $this->assertStringContainsString('/g66', $pdf);
        $this->assertStringContainsString('/Differences', $pdf);
    }

    public function testType3PdfParses(): void
    {
        $doc  = PdfDocument::create();
        $name = $doc->registerFont($this->makeFont(), 'T3');
        $page = $doc->addPage();
        $page->add(Text::write('A')->at(72, 700)->font($name, 40));

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $fonts = $parser->getFonts();
        $this->assertNotEmpty($fonts);
        $this->assertSame('Type3', $fonts[0]['subtype']);
    }

    public function testWidthMeasurement(): void
    {
        $font = $this->makeFont();
        // width = advance(750) * size * FontMatrix[0] (0.001).
        $this->assertEqualsWithDelta(750 * 12 * 0.001, $font->stringWidth('A', 12.0), 0.001);
    }
}
