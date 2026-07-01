<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Metadata\PdfAConformance;
use Papier\Content\ContentStream;
use Papier\Elements\Text;
use Papier\LogicalStructure\{StructElement, StructTreeRoot};
use Papier\Objects\{PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfName};
use Papier\Parser\PdfParser;

final class TaggedPdfTest extends TestCase
{
    private function buildTagged(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Accessible Document');
        $font = $doc->addFont(__DIR__ . '/../examples/Lato-Regular.ttf', '', subset: true);
        $page = $doc->addPage();

        // Author two tagged spans of marked content (MCID 0 and 1).
        $cs = new ContentStream();
        $cs->setFontResolver(fn (string $n) => $doc->getWriter()->getFontMetrics($n));
        $cs->beginMarkedContentMcid('H1', 0)
           ->beginText()->setFont($font, 24)->setTextPosition(72, 750)->showText('Heading')->endText()
           ->endMarkedContent();
        $cs->beginMarkedContentMcid('P', 1)
           ->beginText()->setFont($font, 12)->setTextPosition(72, 720)->showText('A tagged paragraph.')->endText()
           ->endMarkedContent();
        $page->addContent($cs);

        // Build the structure tree: Document → [H1, P], each owning one MCID.
        $tree = new StructTreeRoot();
        $docElem = new StructElement('Document');
        $h1 = new StructElement('H1');
        $h1->addMCID(0, $page->getDictionary());
        $p  = new StructElement('P');
        $p->addMCID(1, $page->getDictionary());
        $docElem->addChild($h1);
        $docElem->addChild($p);
        $tree->addChild($docElem);
        $doc->setStructTree($tree);

        return $doc->toString();
    }

    public function testDocumentIsTaggedWithStructTreeAndParentTree(): void
    {
        $parser = new PdfParser($this->buildTagged());
        $parser->parse();

        $this->assertTrue($parser->isTagged());

        $root = $parser->getStructTreeRoot();
        $this->assertInstanceOf(PdfDictionary::class, $root);
        $this->assertInstanceOf(PdfName::class, $root->get('Type'));
        $this->assertSame('StructTreeRoot', $root->get('Type')->getValue());

        // ParentTree present.
        $pt = $parser->resolve($root->get('ParentTree'));
        $this->assertInstanceOf(PdfDictionary::class, $pt);
        $this->assertInstanceOf(PdfArray::class, $pt->get('Nums'));
    }

    public function testStructElementsHaveParentPageAndMcid(): void
    {
        $parser = new PdfParser($this->buildTagged());
        $parser->parse();
        $root = $parser->getStructTreeRoot();

        // Root → Document
        $docElem = $parser->resolve($root->get('K'));
        if ($docElem instanceof PdfArray) {
            $docElem = $parser->resolve($docElem->get(0));
        }
        $this->assertInstanceOf(PdfDictionary::class, $docElem);
        $this->assertSame('Document', $docElem->get('S')->getValue());

        // Document → [H1, P]
        $kids = $parser->resolve($docElem->get('K'));
        $this->assertInstanceOf(PdfArray::class, $kids);
        $this->assertCount(2, $kids->getItems());

        $h1 = $parser->resolve($kids->get(0));
        $this->assertSame('H1', $h1->get('S')->getValue());
        // /P back-reference to Document, /Pg to the page, /K is the MCID 0.
        $this->assertInstanceOf(PdfIndirectReference::class, $h1->get('P'));
        $this->assertInstanceOf(PdfIndirectReference::class, $h1->get('Pg'));
        $this->assertInstanceOf(PdfInteger::class, $h1->get('K'));
        $this->assertSame(0, $h1->get('K')->getValue());
    }

    public function testPageHasStructParentsAndContentHasMcid(): void
    {
        $pdf    = $this->buildTagged();
        $parser = new PdfParser($pdf);
        $parser->parse();

        $page = $parser->getPages()[0];
        $this->assertInstanceOf(PdfInteger::class, $page->get('StructParents'));

        // The content stream carries the BDC marked-content with MCID.
        $contents = $parser->resolve($page->get('Contents'));
        $decoded  = $contents->decode();
        $this->assertStringContainsString('/H1 <</MCID 0>> BDC', $decoded);
        $this->assertStringContainsString('/P <</MCID 1>> BDC', $decoded);
        $this->assertStringContainsString('EMC', $decoded);
    }

    public function testTaggedPdfACombines(): void
    {
        // Tagging is what makes PDF/A-2a (accessible) meaningful.
        $doc  = PdfDocument::create();
        $doc->setTitle('Tagged PDF/A')->enablePdfA(2, PdfAConformance::Accessible);
        $font = $doc->addFont(__DIR__ . '/../examples/Lato-Regular.ttf', '', subset: true);
        $page = $doc->addPage();
        $cs = new ContentStream();
        $cs->setFontResolver(fn (string $n) => $doc->getWriter()->getFontMetrics($n));
        $cs->beginMarkedContentMcid('P', 0)
           ->beginText()->setFont($font, 12)->setTextPosition(72, 750)->showText('Accessible.')->endText()
           ->endMarkedContent();
        $page->addContent($cs);

        $tree = new StructTreeRoot();
        $p = new StructElement('P');
        $p->addMCID(0, $page->getDictionary());
        $tree->addChild($p);
        $doc->setStructTree($tree);

        $parser = new PdfParser($doc->toString());
        $parser->parse();
        $this->assertTrue($parser->isTagged());
        $this->assertStringContainsString('<pdfaid:conformance>A</pdfaid:conformance>', (string) $parser->getXmpMetadata());
    }
}
