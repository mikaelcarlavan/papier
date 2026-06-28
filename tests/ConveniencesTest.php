<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;
use Papier\Structure\PdfPage;

final class ConveniencesTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . '/papier_conv_' . getmypid();
        @mkdir($this->tmp, 0777, true);
    }

    private function makeDoc(array $pageTexts): string
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        foreach ($pageTexts as $t) {
            $doc->addPage()->add(Text::write($t)->at(72, 750)->font($font, 14));
        }
        $path = $this->tmp . '/' . uniqid('src_') . '.pdf';
        $doc->save($path);
        return $path;
    }

    public function testMergeConcatenatesPages(): void
    {
        $a = $this->makeDoc(['A-one', 'A-two']);
        $b = $this->makeDoc(['B-one']);
        $out = $this->tmp . '/merged.pdf';

        PdfDocument::merge([$a, $b], $out);

        $parser = new PdfParser(file_get_contents($out));
        $parser->parse();
        $this->assertSame(3, $parser->getPageCount());
        $text = $parser->extractText();
        $this->assertStringContainsString('A-one', $text);
        $this->assertStringContainsString('A-two', $text);
        $this->assertStringContainsString('B-one', $text);
    }

    public function testExtractPagesKeepsOrder(): void
    {
        $src = $this->makeDoc(['First', 'Second', 'Third']);
        $out = $this->tmp . '/extract.pdf';

        PdfDocument::extractPages($src, [3, 1], $out);

        $parser = new PdfParser(file_get_contents($out));
        $parser->parse();
        $this->assertSame(2, $parser->getPageCount());
        $this->assertStringContainsString('Third', $parser->extractTextFromPageNumber(1));
        $this->assertStringContainsString('First', $parser->extractTextFromPageNumber(2));
    }

    public function testNUpReducesSheetCount(): void
    {
        $src = $this->makeDoc(['p1', 'p2', 'p3', 'p4']);
        $out = $this->tmp . '/nup.pdf';

        PdfDocument::nUp($src, 2, 2, $out); // 4 pages → 1 sheet

        $parser = new PdfParser(file_get_contents($out));
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
        $this->assertStringContainsString('/Subtype /Form', file_get_contents($out));
    }

    public function testRunningFooterOnEveryPage(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        $doc->addPage()->add(Text::write('Body 1')->at(72, 700)->font($font, 12));
        $doc->addPage()->add(Text::write('Body 2')->at(72, 700)->font($font, 12));

        $doc->footer(function (PdfPage $page, int $n, int $total) use ($font) {
            $page->add(Text::write("Page $n of $total")->at(72, 30)->font($font, 9));
        });

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertStringContainsString('Page 1 of 2', $parser->extractTextFromPageNumber(1));
        $this->assertStringContainsString('Page 2 of 2', $parser->extractTextFromPageNumber(2));
    }

    public function testOddEvenFiltering(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        for ($i = 0; $i < 3; $i++) { $doc->addPage(); }

        $doc->onEachPage(fn(PdfPage $p, int $n) => $p->add(Text::write('ODD')->at(72, 30)->font($font, 9)), 'odd');
        $doc->onEachPage(fn(PdfPage $p, int $n) => $p->add(Text::write('EVEN')->at(72, 30)->font($font, 9)), 'even');

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertStringContainsString('ODD',  $parser->extractTextFromPageNumber(1));
        $this->assertStringContainsString('EVEN', $parser->extractTextFromPageNumber(2));
        $this->assertStringContainsString('ODD',  $parser->extractTextFromPageNumber(3));
        $this->assertStringNotContainsString('EVEN', $parser->extractTextFromPageNumber(1));
    }

    public function testRunningElementsAreIdempotent(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        $doc->addPage();
        $doc->footer(fn(PdfPage $p, int $n, int $t) => $p->add(Text::write("Foot $n")->at(72, 30)->font($font, 9)));

        $first  = $doc->toString();
        $second = $doc->toString(); // must not double the footer

        $p1 = new PdfParser($first);  $p1->parse();
        $p2 = new PdfParser($second); $p2->parse();
        $this->assertSame(
            substr_count($p1->extractText(), 'Foot 1'),
            substr_count($p2->extractText(), 'Foot 1'),
        );
        $this->assertSame(1, substr_count($p2->extractText(), 'Foot 1'));
    }

    public function testPageRotation(): void
    {
        $doc = PdfDocument::create();
        $doc->addPage()->setRotation(90);
        $this->assertStringContainsString('/Rotate 90', $doc->toString());
    }
}
