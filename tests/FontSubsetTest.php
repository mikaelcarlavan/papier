<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Font\TrueTypeFont;
use Papier\Font\TrueTypeSubsetter;
use Papier\Parser\PdfParser;

final class FontSubsetTest extends TestCase
{
    private const FONT = __DIR__ . '/../examples/Lato-Regular.ttf';

    public function testSubsetterProducesSmallerValidFont(): void
    {
        $data = file_get_contents(self::FONT);
        $this->assertNotFalse($data);

        // Keep only a handful of glyphs (e.g. ids reachable from "Hello").
        $font = TrueTypeFont::fromFile(self::FONT);
        $sub  = TrueTypeSubsetter::subset($data, [3 => true, 36 => true, 40 => true]);

        $this->assertNotNull($sub);
        $this->assertLessThan(strlen($data), strlen($sub));

        // The subset must still be a valid sfnt: same numGlyphs, parseable directory.
        $this->assertSame(substr($data, 0, 4), substr($sub, 0, 4)); // sfnt version preserved
        $origGlyphs = $this->numGlyphs($data);
        $this->assertSame($origGlyphs, $this->numGlyphs($sub));
        $this->assertArrayHasKey('glyf', $this->dir($sub));
        $this->assertArrayHasKey('loca', $this->dir($sub));
    }

    public function testSubsetEmbeddedInPdfHasTagAndToUnicode(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont(self::FONT, '', subset: true);
        $page = $doc->addPage();
        $page->add(Text::write('Hello Subset')->at(72, 750)->font($font, 18));
        $pdf = $doc->toString();

        // Subset tag "ABCDEF+" prefix on BaseFont and a ToUnicode CMap present.
        $this->assertMatchesRegularExpression('/\/BaseFont\s*\/[A-Z]{6}\+/', $pdf);
        $this->assertStringContainsString('/ToUnicode', $pdf);
        $this->assertStringContainsString('beginbfchar', $this->firstFlateBlockContaining($pdf, 'beginbfchar'));
    }

    public function testFullEmbedAlsoGetsToUnicode(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont(self::FONT); // no subset
        $page = $doc->addPage();
        $page->add(Text::write('Hello Full')->at(72, 750)->font($font, 18));
        $pdf = $doc->toString();

        $this->assertStringContainsString('/ToUnicode', $pdf);
        // No subset tag when embedding the full program.
        $this->assertDoesNotMatchRegularExpression('/\/BaseFont\s*\/[A-Z]{6}\+/', $pdf);
    }

    public function testSubsetPdfStillParses(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont(self::FONT, '', subset: true);
        $page = $doc->addPage();
        $page->add(Text::write('Subset round trip')->at(72, 750)->font($font, 18));
        $pdf = $doc->toString();

        $parser = new PdfParser($pdf);
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
        $fonts = $parser->getFonts();
        $this->assertNotEmpty($fonts);
    }

    // ── helpers ─────────────────────────────────────────────────────────────────

    private function numGlyphs(string $data): int
    {
        $maxp = $this->dir($data)['maxp'];
        $off  = $maxp['offset'];
        return (ord($data[$off + 4]) << 8) | ord($data[$off + 5]);
    }

    /** @return array<string,array{offset:int,length:int}> */
    private function dir(string $data): array
    {
        $num = (ord($data[4]) << 8) | ord($data[5]);
        $out = [];
        for ($i = 0; $i < $num; $i++) {
            $rec = 12 + $i * 16;
            $tag = substr($data, $rec, 4);
            $out[$tag] = [
                'offset' => unpack('N', substr($data, $rec + 8, 4))[1],
                'length' => unpack('N', substr($data, $rec + 12, 4))[1],
            ];
        }
        return $out;
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
}
