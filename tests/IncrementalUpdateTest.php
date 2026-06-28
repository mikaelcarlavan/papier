<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Metadata\DocumentInfo;
use Papier\Objects\{PdfDictionary, PdfName, PdfString};
use Papier\Parser\PdfParser;
use Papier\Writer\IncrementalUpdater;

final class IncrementalUpdateTest extends TestCase
{
    private function base(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Original Title')->setAuthor('Papier');
        $font = $doc->addFont('Helvetica');
        $page = $doc->addPage();
        $page->add(Text::write('Base content')->at(72, 750)->font($font, 14));
        return $doc->toString();
    }

    public function testIncrementalUpdatePreservesOriginalAndOverrides(): void
    {
        $original = $this->base();

        $parser = new PdfParser($original);
        $parser->parse();
        $infoNum = $parser->getXref()->getInfoObjectNumber();
        $this->assertNotNull($infoNum);

        $updater = new IncrementalUpdater($parser);
        $info = new DocumentInfo();
        $info->setTitle('Updated Title')->setAuthor('Papier');
        $updater->updateObject($infoNum, $info->getDictionary());
        $updated = $updater->build();

        // The original bytes are preserved verbatim as a prefix (true incremental).
        $this->assertStringStartsWith($original, $updated);

        // A /Prev chain and a second revision exist.
        $this->assertStringContainsString('/Prev', $updated);
        $this->assertSame(2, substr_count($updated, '%%EOF'));

        // Re-parsing sees the newer revision win.
        $reparsed = new PdfParser($updated);
        $reparsed->parse();
        $this->assertSame('Updated Title', $reparsed->getTitle());

        // Original content still resolves through the /Prev chain.
        $this->assertSame(1, $reparsed->getPageCount());
        $this->assertStringContainsString('Base content', $reparsed->extractText());
    }

    public function testAddObjectAllocatesBeyondSize(): void
    {
        $parser = new PdfParser($this->base());
        $parser->parse();
        $size = $parser->getXref()->getSize();

        $updater = new IncrementalUpdater($parser);
        $dict = new PdfDictionary();
        $dict->set('Type', new PdfName('TestMarker'));
        $dict->set('Note', new PdfString('hello from a new revision'));
        $newNum = $updater->addObject($dict);

        $this->assertGreaterThanOrEqual($size, $newNum);

        $reparsed = new PdfParser($updater->build());
        $reparsed->parse();
        $obj = $reparsed->resolveObject($newNum);
        $this->assertInstanceOf(PdfDictionary::class, $obj);
        $note = $obj->get('Note');
        $this->assertInstanceOf(PdfString::class, $note);
        $this->assertSame('hello from a new revision', $note->getValue());
    }
}
