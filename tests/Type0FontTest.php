<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Font\Type0Font;
use Papier\Parser\PdfParser;

final class Type0FontTest extends TestCase
{
    private const FONT = __DIR__ . '/../examples/Lato-Regular.ttf';

    public function testCompositeFontEmitsHexGlyphStringAndStructures(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addUnicodeFont(self::FONT, '', subset: true);
        $page = $doc->addPage();
        $page->add(Text::write('Unicode café')->at(72, 750)->font($font, 18));
        $pdf  = $doc->toString();

        // Composite font structures.
        $this->assertStringContainsString('/Subtype /Type0', $pdf);
        $this->assertStringContainsString('/Encoding /Identity-H', $pdf);
        $this->assertStringContainsString('/CIDFontType2', $pdf);
        $this->assertStringContainsString('/CIDToGIDMap /Identity', $pdf);
        $this->assertStringContainsString('/ToUnicode', $pdf);
        // Subset tag on BaseFont.
        $this->assertMatchesRegularExpression('/\/BaseFont\s*\/[A-Z]{6}\+/', $pdf);

        // Text is shown as a hex string (Tj) of two-byte glyph codes, not a literal.
        // The content stream is Flate-compressed, so inflate it first.
        $content = $this->firstFlateBlockContaining($pdf, 'Tj');
        $this->assertNotSame('', $content, 'no inflatable content stream with a Tj operator');
        $this->assertMatchesRegularExpression('/<[0-9A-Fa-f]{4,}>\s*Tj/', $content);
    }

    private function firstFlateBlockContaining(string $pdf, string $needle): string
    {
        if (preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $m)) {
            foreach ($m[1] as $block) {
                $raw = @gzuncompress($block);
                if ($raw !== false && str_contains($raw, $needle)) {
                    return $raw;
                }
            }
        }
        return '';
    }

    public function testWidthMeasurementUsesGlyphAdvances(): void
    {
        $font = Type0Font::fromTrueType(self::FONT, false);
        $w1 = $font->stringWidth('i', 12.0);
        $w2 = $font->stringWidth('W', 12.0);
        $this->assertGreaterThan(0.0, $w1);
        $this->assertGreaterThan($w1, $w2); // 'W' is wider than 'i'
    }

    public function testEncodeTextProducesTwoBytesPerChar(): void
    {
        $font = Type0Font::fromTrueType(self::FONT, false);
        $this->assertTrue($font->isComposite());
        $encoded = $font->encodeText('AB');
        $this->assertSame(4, strlen($encoded)); // two bytes per character
    }

    public function testCompositePdfParsesAndExtractsText(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addUnicodeFont(self::FONT, '', subset: true);
        $page = $doc->addPage();
        $page->add(Text::write('Hello World')->at(72, 750)->font($font, 18));
        $pdf  = $doc->toString();

        $parser = new PdfParser($pdf);
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
        $fonts = $parser->getFonts();
        $this->assertNotEmpty($fonts);
        $this->assertSame('Type0', $fonts[0]['subtype']);
    }
}
