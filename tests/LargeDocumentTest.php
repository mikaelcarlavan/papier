<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

/**
 * Scale/correctness checks. These guard against accidental O(n²) regressions and
 * verify large documents round-trip — they use generous bounds, not tight timing.
 */
final class LargeDocumentTest extends TestCase
{
    public function testManyPagesRoundTrip(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        $n    = 400;
        for ($i = 1; $i <= $n; $i++) {
            $doc->addPage()->add(Text::write("Page $i marker")->at(72, 750)->font($font, 12));
        }
        $pdf = $doc->toString();

        $parser = new PdfParser($pdf);
        $parser->parse();
        $this->assertSame($n, $parser->getPageCount());
        $this->assertStringContainsString('Page 1 marker',   $parser->extractTextFromPageNumber(1));
        $this->assertStringContainsString("Page $n marker",  $parser->extractTextFromPageNumber($n));
    }

    public function testGenerationScalesRoughlyLinearly(): void
    {
        $time = function (int $n): float {
            $doc  = PdfDocument::create();
            $font = $doc->addFont('Helvetica');
            for ($i = 1; $i <= $n; $i++) {
                $doc->addPage()->add(Text::write("Row $i")->at(72, 750)->font($font, 12));
            }
            $t0 = microtime(true);
            $doc->toString();
            return microtime(true) - $t0;
        };

        // Warm up, then compare 200 vs 800 pages: 4× the work should be far
        // below quadratic (which would be ~16×). Allow a generous 9× ceiling.
        $time(50);
        $small = max($time(200), 0.001);
        $large = $time(800);
        $this->assertLessThan($small * 9, $large, 'generation appears worse than linear');
    }

    public function testPagesAreCachedNotRewalked(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        for ($i = 0; $i < 20; $i++) { $doc->addPage(); }

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        // Repeated accessors must return consistent results from the cache.
        $this->assertSame($parser->getPages(), $parser->getPages());
        $this->assertSame(20, $parser->getPageCount());
    }
}
