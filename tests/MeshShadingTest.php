<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\Graphics\Shading\{
    GouraudTriangleShading, LatticeTriangleShading, CoonsPatchShading, TensorPatchShading
};
use Papier\Objects\{PdfInteger, PdfName, PdfStream};

final class MeshShadingTest extends TestCase
{
    public function testGouraudTriangleType4(): void
    {
        $sh = new GouraudTriangleShading('DeviceRGB');
        $sh->addTriangle([0, 0], [1, 0, 0], [100, 0], [0, 1, 0], [50, 100], [0, 0, 1]);
        $stream = $sh->toStream();

        $this->assertShadingType($stream, 4);
        $this->assertSame(8, $this->intEntry($stream, 'BitsPerFlag'));
        $this->assertSame(16, $this->intEntry($stream, 'BitsPerCoordinate'));
        // 3 vertices × (1 flag + 2 + 2 coord + 3 colour) = 24 bytes.
        $this->assertSame(24, strlen($stream->decode()));
    }

    public function testLatticeTriangleType5(): void
    {
        $sh = new LatticeTriangleShading('DeviceRGB', 2);
        // 2×2 grid.
        $sh->addVertex(0, 0, [1, 0, 0])->addVertex(100, 0, [0, 1, 0])
           ->addVertex(0, 100, [0, 0, 1])->addVertex(100, 100, [1, 1, 0]);
        $stream = $sh->toStream();

        $this->assertShadingType($stream, 5);
        $this->assertSame(2, $this->intEntry($stream, 'VerticesPerRow'));
        // 4 vertices × (2 + 2 coord + 3 colour) = 28 bytes.
        $this->assertSame(28, strlen($stream->decode()));
    }

    public function testCoonsPatchType6(): void
    {
        $points = [];
        for ($i = 0; $i < 12; $i++) { $points[] = [$i * 10, ($i % 4) * 10]; }
        $colors = [[1, 0, 0], [0, 1, 0], [0, 0, 1], [1, 1, 0]];

        $sh = (new CoonsPatchShading('DeviceRGB'))->addPatch($points, $colors);
        $stream = $sh->toStream();

        $this->assertShadingType($stream, 6);
        // 1 flag + 12 pts × 4 + 4 colours × 3 = 1 + 48 + 12 = 61 bytes.
        $this->assertSame(61, strlen($stream->decode()));
    }

    public function testTensorPatchType7(): void
    {
        $points = [];
        for ($i = 0; $i < 16; $i++) { $points[] = [$i * 5, ($i % 4) * 5]; }
        $colors = [[1, 0, 0], [0, 1, 0], [0, 0, 1], [1, 1, 0]];

        $sh = (new TensorPatchShading('DeviceRGB'))->addPatch($points, $colors);
        $stream = $sh->toStream();

        $this->assertShadingType($stream, 7);
        // 1 flag + 16 pts × 4 + 4 colours × 3 = 1 + 64 + 12 = 77 bytes.
        $this->assertSame(77, strlen($stream->decode()));
    }

    public function testWrongPatchSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CoonsPatchShading('DeviceRGB'))->addPatch([[0, 0]], [[1, 0, 0]]);
    }

    public function testCmykComponentCount(): void
    {
        $sh = new GouraudTriangleShading('DeviceCMYK');
        $sh->addVertex(0, 0, 0, [0, 0, 0, 1]); // flag, x, y, colour
        // 1 flag + 4 coord + 4 colour = 9 bytes per vertex.
        $this->assertSame(9, strlen($sh->toStream()->decode()));
    }

    // ── helpers ─────────────────────────────────────────────────────────────────

    private function assertShadingType(PdfStream $s, int $type): void
    {
        $st = $s->getDictionary()->get('ShadingType');
        $this->assertInstanceOf(PdfInteger::class, $st);
        $this->assertSame($type, $st->getValue());
        $cs = $s->getDictionary()->get('ColorSpace');
        $this->assertInstanceOf(PdfName::class, $cs);
    }

    private function intEntry(PdfStream $s, string $key): int
    {
        $v = $s->getDictionary()->get($key);
        $this->assertInstanceOf(PdfInteger::class, $v);
        return $v->getValue();
    }
}
