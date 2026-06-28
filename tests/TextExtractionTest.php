<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

final class TextExtractionTest extends TestCase
{
    public function testSimpleFontWithAccents(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        $page = $doc->addPage();
        $page->add(Text::write('Café déjà vu — €5')->at(72, 750)->font($font, 14));

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertStringContainsString('Café déjà vu', $parser->extractText());
    }

    public function testMultipleLinesPreserveBreaks(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        $page = $doc->addPage();
        $page->add(
            Text::write('First line')->at(72, 750)->font($font, 14),
            Text::write('Second line')->at(72, 720)->font($font, 14),
            Text::write('Third line')->at(72, 690)->font($font, 14),
        );

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $text  = $parser->extractText();
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

        $this->assertSame(['First line', 'Second line', 'Third line'], $lines);
    }

    public function testType0SubsetFontExtractsViaToUnicode(): void
    {
        // Composite (Type0) fonts encode 2-byte glyph ids; extraction must use
        // the embedded /ToUnicode CMap — the old regex path produced garbage.
        $doc  = PdfDocument::create();
        $font = $doc->addUnicodeFont(__DIR__ . '/../examples/Lato-Regular.ttf', '', subset: true);
        $page = $doc->addPage();
        $page->add(Text::write('Unicode résumé')->at(72, 750)->font($font, 18));

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertStringContainsString('Unicode résumé', $parser->extractText());
    }

    public function testSubsetSimpleTrueTypeExtracts(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont(__DIR__ . '/../examples/Lato-Regular.ttf', '', subset: true);
        $page = $doc->addPage();
        $page->add(Text::write('Hello Lato')->at(72, 750)->font($font, 18));

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertStringContainsString('Hello Lato', $parser->extractText());
    }

    public function testPerPageExtraction(): void
    {
        $doc  = PdfDocument::create();
        $font = $doc->addFont('Helvetica');
        foreach (['Alpha', 'Beta'] as $i => $word) {
            $page = $doc->addPage();
            $page->add(Text::write($word)->at(72, 750)->font($font, 14));
        }
        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertStringContainsString('Alpha', $parser->extractTextFromPageNumber(1));
        $this->assertStringContainsString('Beta', $parser->extractTextFromPageNumber(2));
        $this->assertStringNotContainsString('Beta', $parser->extractTextFromPageNumber(1));
    }
}
