<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

final class ObjectStreamTest extends TestCase
{
    private function build(bool $compressed): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Compressed Doc')->setAuthor('Papier');
        $font = $doc->addFont('Helvetica');
        for ($i = 1; $i <= 3; $i++) {
            $page = $doc->addPage();
            $page->add(Text::write("Page $i body text")->at(72, 750)->font($font, 14));
        }
        $doc->attachFile('note.txt', 'attached content', 'text/plain');
        if ($compressed) {
            $doc->useObjectStreams();
        }
        return $doc->toString();
    }

    public function testCompressedFileUsesXRefStreamAndIsSmaller(): void
    {
        $classic    = $this->build(false);
        $compressed = $this->build(true);

        // No classic "xref" keyword section; an /ObjStm and /XRef stream exist.
        $this->assertStringContainsString('/ObjStm', $compressed);
        $this->assertStringContainsString('/XRef', $compressed);
        // No classic "\nxref\n" table is emitted in the compressed path.
        $this->assertStringNotContainsString("\nxref\n", $compressed);
        $this->assertLessThan(strlen($classic), strlen($compressed));
    }

    public function testCompressedRoundTripReads(): void
    {
        $pdf    = $this->build(true);
        $parser = new PdfParser($pdf);
        $parser->parse();

        $this->assertSame(3, $parser->getPageCount());
        $this->assertSame('Compressed Doc', $parser->getTitle());
        $this->assertSame('Papier', $parser->getAuthor());
        $this->assertStringContainsString('Page 2 body text', $parser->extractText());

        $atts = $parser->getAttachments();
        $this->assertCount(1, $atts);
        $this->assertSame('attached content', $atts[0]['data']);
    }

    public function testClassicAndCompressedProduceSameText(): void
    {
        $a = new PdfParser($this->build(false)); $a->parse();
        $b = new PdfParser($this->build(true));  $b->parse();
        $this->assertSame(
            trim($a->extractText()),
            trim($b->extractText()),
        );
    }
}
