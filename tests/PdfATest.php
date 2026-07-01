<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Metadata\PdfAConformance;
use Papier\Elements\Text;
use Papier\Color\IccProfile;
use Papier\Parser\PdfParser;

final class PdfATest extends TestCase
{
    private const FONT = __DIR__ . '/../examples/Lato-Regular.ttf';

    private function buildPdfA(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Archival Document')->setAuthor('Papier');
        $doc->enablePdfA(2, PdfAConformance::Basic);
        $font = $doc->addFont(self::FONT, '', subset: true); // embedded font (required)
        $page = $doc->addPage();
        $page->add(Text::write('Archived for the long term.')->at(72, 750)->font($font, 14));
        return $doc->toString();
    }

    public function testOutputIntentAndIccPresent(): void
    {
        $pdf = $this->buildPdfA();

        $this->assertStringContainsString('/OutputIntents', $pdf);
        $this->assertStringContainsString('/S /GTS_PDFA1', $pdf);
        $this->assertStringContainsString('/DestOutputProfile', $pdf);
        // The ICC profile stream is embedded with N = 3 components.
        $this->assertStringContainsString('/N 3', $pdf);
        $this->assertSame(3144, strlen(IccProfile::srgb()));
    }

    public function testXmpDeclaresPdfAConformance(): void
    {
        $parser = new PdfParser($this->buildPdfA());
        $parser->parse();
        $xmp = $parser->getXmpMetadata();

        $this->assertNotNull($xmp);
        $this->assertStringContainsString('http://www.aiim.org/pdfa/ns/id/', $xmp);
        $this->assertStringContainsString('<pdfaid:part>2</pdfaid:part>', $xmp);
        $this->assertStringContainsString('<pdfaid:conformance>B</pdfaid:conformance>', $xmp);
    }

    public function testMetadataStreamIsUncompressed(): void
    {
        // PDF/A requires the XMP packet to be readable (not filtered).
        $pdf = $this->buildPdfA();
        $this->assertSame(1, preg_match('/\/Type\s*\/Metadata.*?stream\r?\n(.*?)\r?\nendstream/s', $pdf, $m));
        $this->assertStringContainsString('<?xpacket', $m[1]); // plaintext, not deflated
    }

    public function testEncryptedPdfACannotBeProduced(): void
    {
        $doc  = PdfDocument::create();
        $doc->enablePdfA(2, PdfAConformance::Basic);
        $doc->encrypt('pw');
        $doc->addPage();

        $this->expectException(\LogicException::class);
        $doc->toString();
    }

    public function testPdfAStillParses(): void
    {
        $parser = new PdfParser($this->buildPdfA());
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
        $this->assertSame('Archival Document', $parser->getTitle());
    }
}
