<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

final class XrefRecoveryTest extends TestCase
{
    private function sample(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Recoverable')->setAuthor('Papier');
        $font = $doc->addFont('Helvetica');
        foreach ([1, 2] as $n) {
            $page = $doc->addPage();
            $page->add(Text::write("Recovery page $n")->at(72, 750)->font($font, 14));
        }
        return $doc->toString();
    }

    public function testRecoversFromCorruptStartxref(): void
    {
        $pdf = $this->sample();
        // Point startxref at a bogus offset.
        $corrupt = preg_replace('/startxref\s+\d+/', "startxref\n999999", $pdf, 1);
        $this->assertNotSame($pdf, $corrupt);

        $parser = new PdfParser($corrupt);
        $parser->parse();

        $this->assertSame(2, $parser->getPageCount());
        $this->assertSame('Recoverable', $parser->getTitle());
        $this->assertStringContainsString('Recovery page 2', $parser->extractText());
    }

    public function testRecoversWhenXrefTableRemoved(): void
    {
        $pdf = $this->sample();
        // Drop everything from the xref keyword onward, leaving only the body.
        $xrefPos = strrpos($pdf, "\nxref");
        $this->assertNotFalse($xrefPos);
        $truncated = substr($pdf, 0, $xrefPos) . "\n%%EOF\n";

        $parser = new PdfParser($truncated);
        $parser->parse();

        // Catalog + page content recover via scanning; the Info dictionary is
        // only reachable through the (now-absent) trailer, so metadata may be lost.
        $this->assertSame(2, $parser->getPageCount());
        $this->assertStringContainsString('Recovery page 1', $parser->extractText());
    }

    public function testRecoveryFindsCatalogWithoutTrailer(): void
    {
        $pdf = $this->sample();
        // Remove the trailer dict's /Root and the xref so recovery must scan
        // for /Type /Catalog directly.
        $xrefPos = strrpos($pdf, "\nxref");
        $body = substr($pdf, 0, $xrefPos) . "\n%%EOF\n";
        $body = preg_replace('/\/Root \d+ \d+ R/', '', $body);

        $parser = new PdfParser($body);
        $parser->parse();
        $this->assertSame(2, $parser->getPageCount());
    }

    public function testValidFileIsNotNeedlesslyRebuilt(): void
    {
        // A normal file must still parse via the regular path.
        $parser = new PdfParser($this->sample());
        $parser->parse();
        $this->assertSame('Recoverable', $parser->getTitle());
        $this->assertSame(2, $parser->getPageCount());
    }
}
