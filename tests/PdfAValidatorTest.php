<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Elements\Text;
use Papier\LogicalStructure\{StructElement, StructTreeRoot};
use Papier\Validation\PdfAValidator;

final class PdfAValidatorTest extends TestCase
{
    private const FONT = __DIR__ . '/../examples/Lato-Regular.ttf';

    public function testConformantPdfAPasses(): void
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Archive')->enablePdfA(2, 'B');
        $font = $doc->addFont(self::FONT, '', subset: true);
        $doc->addPage()->add(Text::write('Archived.')->at(72, 750)->font($font, 12));

        $issues = PdfAValidator::validate($doc->toString());
        $this->assertSame([], $issues, implode("\n", $issues));
    }

    public function testNonEmbeddedFontFails(): void
    {
        // Standard-14 Helvetica is not embedded → not PDF/A.
        $doc  = PdfDocument::create();
        $doc->setTitle('Bad')->enablePdfA(2, 'B');
        $font = $doc->addFont('Helvetica');
        $doc->addPage()->add(Text::write('Hi')->at(72, 750)->font($font, 12));

        $issues = PdfAValidator::validate($doc->toString());
        $this->assertNotEmpty($issues);
        $this->assertStringContainsString('not embedded', implode("\n", $issues));
    }

    public function testPlainDocumentMissingIntentAndXmpFails(): void
    {
        // A normal document (no enablePdfA) lacks the OutputIntent.
        $doc  = PdfDocument::create();
        $font = $doc->addFont(self::FONT, '', subset: true);
        $doc->addPage()->add(Text::write('Plain')->at(72, 750)->font($font, 12));

        $issues = PdfAValidator::validate($doc->toString());
        $this->assertNotEmpty($issues);
        $this->assertStringContainsString('OutputIntent', implode("\n", $issues));
    }

    public function testLevelARequiresTagging(): void
    {
        // Declare A conformance but provide no structure tree → must fail.
        $doc  = PdfDocument::create();
        $doc->setTitle('Untagged A')->enablePdfA(2, 'A');
        $font = $doc->addFont(self::FONT, '', subset: true);
        $doc->addPage()->add(Text::write('No tags')->at(72, 750)->font($font, 12));

        $issues = PdfAValidator::validate($doc->toString());
        $this->assertStringContainsString('Tagged PDF', implode("\n", $issues));
    }

    public function testTaggedLevelAPasses(): void
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Tagged A')->enablePdfA(2, 'A');
        $font = $doc->addFont(self::FONT, '', subset: true);
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

        $issues = PdfAValidator::validate($doc->toString());
        $this->assertSame([], $issues, implode("\n", $issues));
    }
}
