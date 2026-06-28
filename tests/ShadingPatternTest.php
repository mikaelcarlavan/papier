<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Content\ContentStream;
use Papier\Graphics\Pattern\ShadingPattern;
use Papier\Graphics\Shading\{AxialShading, GouraudTriangleShading};
use Papier\Objects\{PdfInteger, PdfName};
use Papier\Parser\PdfParser;

final class ShadingPatternTest extends TestCase
{
    public function testAxialShadingPatternDictionary(): void
    {
        $axial = new AxialShading('DeviceRGB', 0, 0, 200, 0);
        $pat   = new ShadingPattern($axial);
        $dict  = $pat->getDictionary();

        $this->assertSame('Pattern', $dict->get('Type')->getValue());
        $this->assertSame(2, $dict->get('PatternType')->getValue());
        $this->assertNotNull($dict->get('Shading'));
    }

    public function testFillShapeWithAxialShadingPattern(): void
    {
        $doc  = PdfDocument::create();
        $page = $doc->addPage();

        $axial = new AxialShading('DeviceRGB', 72, 0, 472, 0);
        $pat   = new ShadingPattern($axial);
        $page->getResources()->addPattern('SP1', $pat->getDictionary());

        $cs = new ContentStream();
        $cs->save()
           ->setFillColorSpace('Pattern')->setFillColorN('SP1')
           ->drawRect(72, 600, 400, 100)->fill()
           ->restore();
        $page->addContent($cs);

        $pdf = $doc->toString();
        $this->assertStringContainsString('/PatternType 2', $pdf);

        // The content stream (compressed) selects the Pattern colour space.
        $content = $this->inflateContaining($pdf, 'scn');
        $this->assertStringContainsString('/Pattern cs', $content);
        $this->assertStringContainsString('/SP1 scn', $content);

        $parser = new PdfParser($pdf);
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
    }

    private function inflateContaining(string $pdf, string $needle): string
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

    public function testMeshShadingPatternPromotesNestedStream(): void
    {
        $doc  = PdfDocument::create();
        $page = $doc->addPage();

        $mesh = new GouraudTriangleShading('DeviceRGB');
        $mesh->addTriangle([72, 600], [1, 0, 0], [272, 600], [0, 1, 0], [172, 740], [0, 0, 1]);
        $pat = new ShadingPattern($mesh); // mesh → stream /Shading

        $page->getResources()->addPattern('MP1', $pat->getDictionary());
        $cs = new ContentStream();
        $cs->save()->setFillColorSpace('Pattern')->setFillColorN('MP1')
           ->drawRect(72, 600, 200, 140)->fill()->restore();
        $page->addContent($cs);

        $pdf = $doc->toString();
        // The nested mesh shading (type 4) must be promoted to an indirect stream.
        $this->assertStringContainsString('/ShadingType 4', $pdf);
        $this->assertMatchesRegularExpression('/\/Shading \d+ \d+ R/', $pdf);

        $parser = new PdfParser($pdf);
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
    }
}
